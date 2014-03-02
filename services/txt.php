<?php

# headers
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

# requirements
require_once '../scripts/utilities.php';

# get the message
echo GetResponse( $_REQUEST['msg'] );

function GetResponse( $m ){
  switch( true ){
    case preg_match( '/\\A[0-9\\.,]+@[0-9]{1,3}\\z/', $m ):
      $a = explode( '@', $m );
      $r = process( array( 'check'   => $a[0],
                           'percent' => ( $a[1] / 100 ),
                           'medium'  => 'S' ) );
      $msg = "{$r['check']} + {$r['tip']} = {$r['total']}";
      break;
    case $m == 'help':
      $msg = 'I calculate tip & total based on check amount and %. ' .
             'e.g. "TIPR 20.22@18" = 18% tip on a $20.22 check.';
      break;
    case $m == '':
      $msg = 'Welcome to tipr. I calculate tip & total based on check ' .
             'amount and %. e.g. "TIPR 20.22@18" = 18% tip on a $20.22 check.';
      break;
    default:
      $msg = 'there was something wrong with your request, ' .
             'please try again or type "TIPR help" for more information.';
      break;
  }
  return $msg;
}

?>