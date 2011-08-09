<?php
/*
 * MakeFriends
 * Handles making friends with folks
 * Should run every 30 seconds
 */
require_once 'config.php';
require_once '../classes/TwitterBot.php';

$TB = new TwitterBot( array( 'user'  => $config['user'],
                             'pass'  => $config['pass'],
                             'debug' => 'ERRORS' ) );
$TB->MakeFriends( 'Thanks for becoming my friend, you can now use Tipr via Twitter. ' .
                  'd tipr help for info on how to use the service.' );

?>