<?php

#------------------------------------------------------------------------------#
#                                  UTILITIES                                   #
#------------------------------------------------------------------------------#

# debugging stuff
$DEBUG = ( empty( $_REQUEST['debug'] ) ) ? 0 : 1;

# assertion stuff
assert_options( ASSERT_ACTIVE,     1 );
assert_options( ASSERT_WARNING,    0 );
assert_options( ASSERT_QUIET_EVAL, 1 );
assert_options( ASSERT_CALLBACK,   'assert_handler' );

# DB
require_once 'classes/mysql.php';
if ( $_SERVER['HTTP_HOST'] == 'localhost' ||
     strpos( $_SERVER['HTTP_HOST'], 'dev' ) !== FALSE )
{
  include 'cparams.php';
} else {
  include '/var/www/mysql/tipr_mobi.php';
}
$MYSQL = &new mysql( $mhost, $muser, $mpassword, $mdatabase, 0 );

$PINK = ( !empty( $_REQUEST['pink'] ) ) ? true : pink();

/*
 * Process
 * processes the tip amount and total from the given $_REQUEST
 *
 */
function process( $info ){
  # convert $info['total']
  $info['check'] = number_format( str_replace( Array( ',', ' ' ), '', $info['check'] ), 2, '.', '' );
  showMe( $info, '$info' );
  # get the original tip
  $o_tip = round( $info['check'] * $info['percent'], 2 );
  # test and new tip amounts
  $n_tip = 0;
  $t_tip = $o_tip;
  # rounding info (making sure we're not way undertipping)
  while( $n_tip < $o_tip ){
    # find the base total
    $base = number_format( $info['check'] + $t_tip, 2, '.', '' );
    # figure out the # of digits
    $digits = floor( ( strlen( $base ) - 1)/2 );
    # make $base a string
    settype( $base, 'string' );
    showMe( $base, '$base' );
    # explode
    $arr = preg_split('//', $base, -1, PREG_SPLIT_NO_EMPTY);
    showMe( $arr, '$arr' );
    # create the palindrome
    for( $i=count( $arr )-1, $j=0; $j<$digits; $i--, $j++ ){
      if( $arr[$i] == '.' ){
        $j--;
      } else {
        $arr[$i] = $arr[$j];
      }
    }
    showMe( $arr, '$arr' );
    # build the total
    $total = implode( '', $arr );
    settype( $total, 'float' );
    showMe( $total, '$total' );
    # get the tip amount
    $n_tip = $total - $info['check'];
    showMe( $n_tip, '$n_tip' );
    showMe( $o_tip, '$t_tip' );
    # if it's less, add a dollar
    if( $n_tip < $t_tip ) $t_tip += 1;
  }
  # store
  store( $info, $n_tip, $total );
  
  # build the message
  $message = 'Ok, here ya go:';
  
  /* Should this person win anything?
     Only web-based users are eligible */
  if( $info['medium'] == 'P' ||
      $info['medium'] == 'A' ){
    $winner =  isWinner( $info, $n_tip, $total );
    if( $winner ){
      
      $message = '<strong>Congratulations, you are our ' . ( makeOrdinal( $winner ) ) .
                 ' tipr!</strong> To commemorate this event, we&#8217;d like to ' .
                 ( ( $total > 100 ) ? 'give you $100 toward' : 'pay for' ) .
                 ' your meal. Please <a href="/winner/' . 
                 base64_encode( serialize( array( 'info' => $info,
                                                  'tip' => $n_tip,
                                                  'total' => $total,
                                                  'number' => $winner ) ) ) .
                 '">give us your info</a> and we&#8217;ll send you some money.';
    }
  }
  
  # return the new info
  return Array( 'check'   => $info['check'],
                'tip'     => number_format( $n_tip, 2 ),
                'total'   => number_format( $total, 2 ),
                'message' => $message );
}

/* -----------------------------------------------------------------------------
 * Function: getPromo()
 * Purpose:  returns the promo
 * ---------------------------------------------------------------------------*/
function getPromo(){
  $arr = array( 'Did you know you can use Tipr via text? <a href="/text.php">Learn more</a>.',
                'Did you know you can use Tipr via Twitter? <a href="/twitter.php">Learn more</a>.', 
                'Like using Tipr? <a href="/tips.php">Consider donating</a>.' );
  return selectAtRandom( $arr );
}

/* -----------------------------------------------------------------------------
 * Function: store( $info, $tip, $total )
 * Purpose:  Stores tip calculations to the DB
 * ---------------------------------------------------------------------------*/
function store( $info, $tip, $total){
  # get MySQL
  $mysql = $GLOBALS['MYSQL'];

  # build the query
  $query  = "INSERT INTO `calculations`
               ( `bill`,             `tip_percent`,
                 `tip_amount`,       `total`,
                 `medium` )
             VALUES
               ( '{$info['check']}', '{$info['percent']}',
                 '$tip',             '$total',
                 '{$info['medium']}' )";
  showMe( $query, '$query' );

  # run it
  $mysql->query( $query );
}

/* -----------------------------------------------------------------------------
 * Function: isWinner( $info, $tip, $total )
 * Purpose:  Determines if the person using Tipr is a winner
 * ---------------------------------------------------------------------------*/
function isWinner( $info, $tip, $total ){
  # winners need one of these ids
  $winners = array( 100001, 250052, 500005, 1000001 );
  
  # get MySQL
  $mysql = $GLOBALS['MYSQL'];
  
  # today
  $today = date( 'Ymd' );
  
  # build the query
  $query  = "SELECT `id`
             FROM   `calculations`
             WHERE  ABS(`bill` - {$info['check']}) <= 0.0001
               AND  ABS(`tip_percent` - {$info['percent']}) <= 0.0001
               AND  ABS(`tip_amount` - $tip) <= 0.0001
               AND  ABS(`total` - $total) <= 0.0001
               AND  `medium` = '{$info['medium']}'
               AND  DATE_FORMAT( `date`, '%Y%m%d' ) = '$today'
            ORDER BY `id` DESC
             LIMIT  1";
  showMe( $query, '$query' );

  # run it
  $mysql->query( $query );
  if( $mysql->num_rows() > 0 ){
    while( $mysql->movenext() ){
      $id = $mysql->getfield( 'id' );
    }
  }
  showMe( $id, 'id' );
  
  return in_array( $id, $winners ) ? $id : false;
  
}


/* -----------------------------------------------------------------------------
 * Function: makeOrdinal( $num )
 * Purpose:  Turns a number into an ordinal
 * ---------------------------------------------------------------------------*/
function makeOrdinal( $num ){
  assert( 'is_numeric($value)' );
  switch( true ){
    # 11, 12 & 13 are odd
    case ( substr( $num, -2, 2 ) == 11 ):
    case ( substr( $num, -2, 2 ) == 12 ):
    case ( substr( $num, -2, 2 ) == 13 ):
      $str = 'th';
      break;
    case ( substr( $value, -1, 1 ) == 1 ):
      $str = 'st';
      break;
    case ( substr($value, -1, 1) == 2 ):
      $str = 'nd';
      break;
    case ( substr($value, -1, 1) == 3 ):
      $str = 'rd';
      break;
    default:
      $str = 'th';
      break;
  }
  return $num . $suffix;
}

/* -----------------------------------------------------------------------------
 * Function: mobilize( $buffer )
 * Purpose:  Compresses HTML for mobile delivery
 * ---------------------------------------------------------------------------*/
function mobilize( $buffer ){
  # remove new lines & tabs
  $buffer = preg_replace( '/[\n\r\t]+/', '', $buffer );
  # remove extra whitespace
  $buffer = preg_replace( '/\s{2,}/', ' ', $buffer );
  # remove CSS & JS comments
  $buffer = preg_replace( '/\/\*.*?\*\//i', '', $buffer );
  # return
  return $buffer;
}

/* -----------------------------------------------------------------------------
 * Function: pink()
 * Purpose:  Determinesif it is Pink for October time
 * ---------------------------------------------------------------------------*/
function pink(){
  return dateIsBetween( '10-01', '10-31' );
}

/* -----------------------------------------------------------------------------
 * Function:  dateIsBetween( $start, $end )
 * Purpose:   Determines if it today falls between $start and $end
 * Arguments: $start = a string in mm-dd format
 *            $end   = a string in mm-dd format
 * ---------------------------------------------------------------------------*/
function dateIsBetween( $start, $end ){
  $start = explode( '-', $start );
  $end   = explode( '-', $end );
  $s     = date( 'U', mktime( -12, 0, 0, $start[0], $start[1], date( 'Y' ) ) );
  $e     = date( 'U', mktime( 36, 0, 0, $end[0], $end[1], date( 'Y' ) ) );
  $z     = date( 'Z' ) * -1;
  $now   = time( $z );
  return ( $now >= $s && $now <= $e ) ? true : false;
}

/* -----------------------------------------------------------------------------
 * Function: selectAtRandom( $str )
 * Purpose:  Selects a random element from an array
 * ---------------------------------------------------------------------------*/
function selectAtRandom( $arr ){
  # is it an array?
  assert( 'is_array( $arr )' );
  $no = rand( 0, ( count($arr)-1 ) );
  return $arr[$no];
}


/* =============================================================================
 *                           DEBUGGING FUNCTIONS
 * ===========================================================================*/
/* -----------------------------------------------------------------------------
 * Function: showMe( $var )
 * Purpose:  Dumps a variable to the screen
 * ---------------------------------------------------------------------------*/
function showMe( $var, $varName ) {
  if ( $GLOBALS['DEBUG'] == 1 ) {
    echo '<div style="background: #fff;
                      color: #000;
                      padding: 10px 20px;
                      margin: 5px;
                      width: 50%;
                      border: 2px solid #000;
                      position: relative;
                      z-index: 1000;">';
    echo '<h2 style="margin: 0 -10px;
                     padding: 0;">'.$varName.':</h2>';
    echo ( is_array($var) ) ? '<pre style="margin: 0;
                                           padding: 0;">'
                            : '<samp>';
    print_r( $var );
    echo ( is_array($var) ) ? '</pre>' : '</samp>';
    echo '</div>';
  }
}

/* -----------------------------------------------------------------------------
 * Function: alert( $str )
 * Purpose:  Dumps text messages to the screen (for debugging)
 * ---------------------------------------------------------------------------*/
function alert( $str ) {
  if ( $GLOBALS['DEBUG'] == 1 ) {
    echo '<div style="background: #fff;
                      color: #000;
                      padding: 10px 20px;
                      margin: 5px;
                      width: 50%;
                      border: 2px solid #000;">';
    echo '<h2 style="margin: 0 -10px;
                     padding: 0;">Alert:</h2>';
    echo "<p>$str</p>";
    echo '</div>';
  }
}

/* -----------------------------------------------------------------------------
 * Function: assert_handler( $file, $line, $code )
 * Purpose:  Dumps a human-readable assert error to the screen
 * ---------------------------------------------------------------------------*/
function assert_handler( $file, $line, $code ) {
   echo '<div style="background: #fff;
                     color: #000;
                     padding: 10px;
                     margin: 5px;
                     width: 50%;
                     border: 2px solid #000;"><h4>Assertion Failed</h4>';
   echo "<strong>File:</strong> $file<br />
         <strong>Line:</strong> $line<br />
         <strong>Code:</strong> '$code'</div>";
   exit;
}


?>
