<?php
/*
 * ProcessOutbox
 * Processes the messages that need sending
 * Should run every 10 seconds
 */
require_once 'config.php';
require_once '../classes/TwitterBot.php';

$TB = new TwitterBot( array( 'user'  => $config['user'],
                             'pass'  => $config['pass'],
                             'debug' => 'ERRORS' ) );
$TB->ProcessOutbox();

?>