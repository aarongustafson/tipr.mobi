<?php
/*
 * GetMessages
 * Handles getting new messages into the inbox
 * Should run every 60 seconds
 */
require_once 'config.php';
require_once '../classes/TwitterBot.php';

$TB = new TwitterBot( array( 'user'  => $config['user'],
                             'pass'  => $config['pass'],
                             'debug' => 'ERRORS' ) );
$TB->GetMessages();

?>
