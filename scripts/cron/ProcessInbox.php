<?php
/*
 * MakeFriends
 * Handles manking friends with folks
 * Should run every 10 seconds
 */
require_once 'config.php';
require_once '../classes/TwitterBot.php';
require_once '../utilities.php';

$TB = new TwitterBot( array( 'user'  => $config['user'],
                             'pass'  => $config['pass'],
                             'debug' => 'ERRORS' ) );

$messages = $TB->GetInboxMessages();
print_r( $messages );
if( is_array( $messages ) ){
  foreach( $messages as $m ){
    $response = GetResponse( $m['msg'] );
    if( $TB->QueueMessage( $m['id'], $m['usr'], $response ) ){
      $TB->DeleteMessage( $m['id'], 'inbox' );
    } else {
      $TB->MarkAsUnprocessed( $m['id'], 'inbox' );
    }
  }
}

function GetResponse( $m ){
  switch( true ){
    case preg_match( '/\\A[0-9\\.,]+@[0-9]{1,3}\\z/', $m ):
      $a = explode( '@', $m );
      $r = process( array( 'check'   => $a[0],
                           'percent' => ( $a[1] / 100 ),
                           'medium'  => 'T' ) );
      $msg = "{$r['check']} + {$r['tip']} = {$r['total']}";
      break;
    case $m == 'help':
      $msg = 'I calculate tip & total based on check amount and %. ' .
             'e.g. "d tipr 20.22@18" = 18% tip on a $20.22 check.';
      break;
    default:
      $msg = 'there was something wrong with your request, ' .
             'please try again or type "d tipr help" for more information.';
      break;
  }
  return $msg;
}

?>