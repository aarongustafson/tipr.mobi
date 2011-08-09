<?php
/*******************************************************************************
 * TwitterBot Class
 *******************************************************************************
 *
 * Author:     Aaron Gustafson
 * Email:      aaron@easy-designs.net
 * Website:    http://easy-designs.net
 *
 * File:       class.TwitterBot.php
 * Version:    1.00
 * Copyright:  (c) 2007 - Aaron Gustafson
 *             You are free to use, distribute, and modify this software
 *             under the terms of the MIT License. See LICENSE for more
 *             information
 *
 *******************************************************************************
 * CHANGELOG:
 *
 * * 1.0 [2007-07-17] - Initial Version
 *
 *******************************************************************************
 * DESCRIPTION:
 *
 * This class automates Bot-related commands to Twitter
 *
 *******************************************************************************
 * REQUIREMENTS:
 *
 * This class uses PEAR's HTTP_Request module, FastJSON, and a MySQL wrapper
 *
 *******************************************************************************
 */
require_once 'HTTP/Request.php';
require_once 'JSON.php';
require_once 'mysql.php';

Class TwitterBot {

  # private variables
  private $user;
  private $pass;
  private $HTTP_config;
  private $base_uri = 'http://twitter.com';
  private $mysql;
  private $JSON;

  # Constructor
  function __construct( $config ){

    # get configuration
    foreach( $config as $k=>$v ){
      $this->{$k} = $v;
    }

    # set the $HTTP_config
    $this->HTTP_config = array( 'method'      => 'POST',
                                'user'        => $this->user,
                                'pass'        => $this->pass,
                                'timeout'     => '5',
                                'readTimeout' => '5' );

    # get the directory
    $path = strtolower( __FILE__ );
    if( strpos( $path, '\\' ) ) $path = str_replace( '\\', '/', $path );
    if( !empty( $_SERVER['DOCUMENT_ROOT'] ) ){
      $path = str_replace( strtolower( $_SERVER['DOCUMENT_ROOT'] ), '', $path );
    }
    $path = explode( '/', $path );
    array_pop( $path );
    $directory = implode( '/', $path ) . '/';

    # set the logfile
    $this->logfile = $_SERVER['DOCUMENT_ROOT'] . $directory . 'TwitterBot.log';
    if( $this->purge == true ) $this->PurgeLog();
    $this->log( 'm', __METHOD__, 'started with debugging enabled' );

    # set the mysql connection
    if( $_SERVER['HTTP_HOST'] == 'localhost' ){
      include '../cparams.php';
    } else {
      include '/var/www/mysql/tipr_mobi.php';
    }
    $this->mysql = &new mysql( $mhost, $muser, $mpassword, $mdatabase, 0 );

    # set up the JSON interpreter
    $this->JSON = &new Services_JSON( SERVICES_JSON_LOOSE_TYPE );

  }

  /**
   * TwitterBot::MakeFriends()
   * Collects all friend requests and replies to them
   */
  function MakeFriends( $msg ){

    # get the config
    $config = $this->HTTP_config;
    $config['method'] = 'GET';

    # collect friends
    $req = new HTTP_Request( $this->base_uri . '/friend_requests', $config );
    $req->sendRequest();
    $this->Log( 'm', __METHOD__, 'request sent' );

    # get the response
    $code = $req->getResponseCode();
    # make sure we got the correct response
    if( empty( $code ) || $code != '200' ){
      if( $code == '401' ){
        $this->Log( 'e', __METHOD__, 'Twitter authorization failed' );
        return false;
      } else {
        $this->Log( 'e', __METHOD__, 'Twitter responded incorrectly to this query. Code: ' . $code );
        return false;
      }
    }

    # get the response
    $response = $req->getResponseBody();

    # extract the friend requests
    preg_match_all('/"\/friend_requests\/accept\/[0-9]+?"/', $response, $requests, PREG_PATTERN_ORDER);
    if( is_array( $requests[0] ) ){
      foreach( $requests[0] as $path ){
        $path = str_replace( '"', '', $path );
        $this->Log( 'm', __METHOD__, 'Friend request path: ' . $path );
        # accept the friendship
        $accept = new HTTP_Request( $this->base_uri . $path, $config );
        $accept->sendRequest();
        $this->Log( 'm', __METHOD__, 'Friendship accepted.' );

        /* TODO: Add a way to notify people that we're friends now */
      }
    }
  }

  /**
   * TwitterBot::GetMessages()
   * Checks for direct messages
   */
  public function GetMessages(){
    $this->Log( 'm', __METHOD__, 'script started' );

    # get the config
    $config = $this->HTTP_config;
    $config['method'] = 'GET';

    # get since string
    $since = '';
    $this->mysql->query( "SELECT DATE_FORMAT( `last_query`,
                                              '%a, %e %b %Y %T GMT' ) AS `last_query`
                          FROM   `twitterbot_timestamp`
                          LIMIT  1" );
    if( $this->mysql->num_rows() > 0 ){
      while( $this->mysql->movenext() ){
        $since .= '?since=' . urlencode( $this->mysql->getfield( 'last_query' ) );
      }
    }

    # timestamp (for later)
    $now = date( 'Y-m-d H:i:s' );

    # collect direct messages
    $url = $this->base_uri . '/direct_messages.json' . $since;
    $req = new HTTP_Request( $url, $config );
    $req->sendRequest();
    $this->Log( 'm', __METHOD__, 'request sent: ' . $url );

    # get the response
    $code = $req->getResponseCode();
    # make sure we got the correct response
    if( empty( $code ) || $code != '200' ){
      if( $code == '401' ){
        $this->Log( 'e', __METHOD__, 'Twitter authorization failed' );
        return false;
      } else {
        $this->Log( 'e', __METHOD__, 'Twitter responded incorrectly to this query: ' . $code );
        return false;
      }
    }

    # get the response
    $response = $req->getResponseBody();
    $this->Log( 'm', __METHOD__, 'Response: ' . $response );

    # extract the messages
    $messages = $this->JSON->decode( $response );
    # log them
    if( is_array( $messages ) ){
      foreach( $messages as $m ){
        if( $this->QueueMessage( $m['id'], $m['sender_screen_name'], $m['text'], 'inbox' ) ){
          $this->Log( 'm', __METHOD__, 'Inbound message queued' );
          $delete = new HTTP_Request( $this->base_uri . '/direct_messages/destroy/' . $m['id'], $config );
          $delete->sendRequest();
          $this->Log( 'm', __METHOD__, 'Message deleted from server.' );
        }
      }
      # store the time
      $this->mysql->query( "UPDATE `twitterbot_timestamp`
                            SET    `last_query` = '$now'
                            WHERE  `id` = 0" );
    }

    $this->Log( 'm', __METHOD__, 'script complete' );
  }

  /**
   * TwitterBot::GetInboxMessages()
   * Processes the queue and does what it needs to do
   */
  public function GetInboxMessages(){
    # designate this process' batch
    $batch = date( 'YmdHis' );
    $sql = "UPDATE `twitterbot_inbox`
            SET    `status` = 'P',
                   `batch`  = '$batch'
            WHERE  `status` = 'U'";
    $this->mysql->query( $sql );

    # get the messages
    $sql = "SELECT *
            FROM   `twitterbot_inbox`
            WHERE  `status` = 'P'
              AND  `batch`  = '$batch'";
    $this->mysql->query( $sql );

    # process them
    if( $this->mysql->num_rows() > 0 ){
      $messages = array();
      while( $this->mysql->movenext() ){
        array_push( $messages, array( 'id'  => $this->mysql->getfield( 'id' ),
                                      'usr' => $this->mysql->getfield( 'user' ),
                                      'msg' => $this->mysql->getfield( 'message' ) ) );
      }
      return $messages;
    } else {
      $this->log( 'm', __METHOD__, 'No queued messages' );
      return true;
    }
  }

  /**
   * TwitterBot::ProcessOutbox()
   * Processes the queue and does what it needs to do
   */
  public function ProcessOutbox(){
    # designate this process' batch
    $batch = date( 'YmdHis' );
    $sql = "UPDATE `twitterbot_outbox`
            SET    `status` = 'P',
                   `batch`  = '$batch'
            WHERE  `status` = 'U'";
    $this->mysql->query( $sql );

    # get the messages
    $sql = "SELECT *
            FROM   `twitterbot_outbox`
            WHERE  `status` = 'P'
              AND  `batch`  = '$batch'";
    $this->mysql->query( $sql );

    # process them
    if( $this->mysql->num_rows() > 0 ){
      while( $this->mysql->movenext() ){
        $id = $this->mysql->getfield( 'id' );
        if( $this->SendMessage( $this->mysql->getfield( 'user' ),
                                $this->mysql->getfield( 'message' ) ) ){
          $this->DeleteMessage( $id );
        } else {
          $this->MarkAsUnprocessed( $id );
          return false;
        }
      }
    } else {
      $this->log( 'm', __METHOD__, 'No queued messages' );
    }
    return true;
  }

  /**
   * TwitterBot::QueueMessage()
   * Queues a message for processing
   */
  public function QueueMessage( $id, $usr, $msg, $table='outbox' ){
    $sql = "INSERT INTO `twitterbot_{$table}`
              ( `id`,  `user`, `message` )
            VALUES
              ( '$id', '$usr', '$msg' )";
    if( !$this->mysql->query( $sql ) ){
      $this->Log( 'e', __METHOD__, 'Failed to queue a message. SQL: ' . $sql );
      return false;
    }
    return true;
  }

  /**
   * TwitterBot::DeleteMessage()
   * removes a message from the queue
   */
  public function DeleteMessage( $id, $table='outbox' ){
    $sql = "DELETE FROM `twitterbot_{$table}`
            WHERE  `id` = '$id'
            LIMIT  1";
    if( !$this->mysql->query( $sql ) ){
      $this->Log( 'e', __METHOD__, 'Failed to delete a message. SQL: ' . $sql );
      return false;
    }
    return true;
  }

  /**
   * TwitterBot::MarkAsUnprocessed()
   * Marks a message as pending processing
   */
  private function MarkAsUnprocessed( $id, $table='outbox' ){
    $sql = "UPDATE `twitterbot_{$table}`
            SET    `status` = 'U',
                   `batch`  = NULL
            WHERE  `id` = '$id'
            LIMIT  1";
    if( !$this->mysql->query( $sql ) ){
      $this->Log( 'e', __METHOD__, 'Failed to mark a queued message as "unprocessed". SQL: ' . $sql );
      return false;
    }
    return true;
  }

  /**
   * TwitterBot::SendMessage()
   * Sends a direct message
   *
   * @param $user = person to send it to
   * @param $msg  = message contents
   */
  private function SendMessage( $user, $msg ){
    # send the message
    $req = new HTTP_Request( $this->base_uri . '/direct_messages/new.json', $this->HTTP_config );
    $req->addPostData( 'user', $user );
    $req->addPostData( 'text', $msg );
    $req->sendRequest();

    # get the response
    $code = $req->getResponseCode();
    # make sure we got the correct response
    if( empty( $code ) || $code != '200' ){
      if( $code == '401' ){
        $this->Log( 'e', __METHOD__, 'Twitter authorization failed' );
        return false;
      } else {
        $this->Log( 'e', __METHOD__, 'Twitter responded incorrectly to this query: ' . $code );
        return false;
      }
    }
    return true;
  }

  /**
   * TwitterBot::Log()
   * Logs messages to the logfile
   *
   * @param $msg_type = the type of message ( 'm', 'e' or 'w' )
   * @param $method   = the locator method called
   * @param $msg      = the message to log
   */
  private function Log( $msg_type, $method, $msg ){
    if( !empty( $this->debug ) ){
      $types = Array( 'm' => 'MESSAGE - ',
                      'e' => 'ERROR - ',
                      'w' => 'WARNING - ' );
      $debug = explode( ' && ', $this->debug );
      switch( true ){
        case ( in_array( 'ALL', $debug ) ):
        case ( in_array( 'ERRORS', $debug ) &&
               $msg_type == 'e' ):
        case ( in_array( 'WARNINGS', $debug ) &&
               $msg_type == 'w' ):
          $log = true;
          break;
        default:
          $log = false;
          break;
      }
      if( $log === true ){
        error_log( date( 'Y-m-d H:i:s - ' ) . $types[$msg_type] .
                   $method . ' - ' .
                   $msg . "\r\n", 3, $this->logfile );
      }
    }
  }

  /**
   * TwitterBot::PurgeLog()
   * Purges the logfile
   */
  private function PurgeLog(){
    unlink( $this->logfile );
  }
}
?>
