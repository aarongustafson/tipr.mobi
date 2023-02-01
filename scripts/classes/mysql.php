<?php

class mysql {

  /* public: connection parameters */
  var $debug;
  var $host;
  var $database;
  var $user;
  var $password;

  /* private: connection parameters */
  var $conn;
  var $rstemp;
  var $record;


  /**
   * mysql::mysql()
   * Constructor this class - define public connection parameters and
   * call the connect method
   *
   * @param $host
   * @param $user
   * @param $password
   * @param $database
   */
  function mysql ($host,$user,$password,$database,$debug=0) {

    $this->debug = $debug;
    if ($this->debug) echo "\n\nDebug On <br>\n";

    $this->host = $host;
      $this->user = $user;
      $this->password = $password;
      $this->database = $database;

    /**
         * open connection
         */
    $this->connect();
  }


        /**
         * mysql::connect()
         * Open connection with the server
         *
         * @return id da conexao
         */
         function connect () {

             /**
              * Open connection
              */
             if ($this->debug) echo "Connecting  to $this->host <br>\n";
             $this->conn = @mysql_pconnect($this->host,$this->user,$this->password)
                      or die("Connection to $server failed <br>\n");

            /**
             * Select to database
             */
             if ($this->debug) echo "Selecting to $this->database <br>\n";
             @mysql_select_db($this->database,$this->conn)
                      or die("Error:" . mysql_errno() . " : " . mysql_error() . "<br>\n");

             return $this->conn;
        }

  /**
   * mysql::query()
   * Execute SQL
   *
   * @param $sql
   * @return
   */
  function query($sql) {

    if ($this->debug) echo "Run SQL:  $sql <br>\n\n";
    $this->rstemp = @mysql_query($sql,$this->conn)
                or die("Error:" . mysql_errno() . " : " . mysql_error() . "<br>\n");

    return $this->rstemp;

  }

  /**
   * mysql::num_rows()
   * return number of records in current select
   *
   * @param $rstemp
   * @return
   */
  function num_rows() {

    $num = @mysql_num_rows($this->rstemp);
    if ($this->debug) echo "$num records returneds <br>\n\n";

    return $num;
  }


    /**
     * mysql::movenext()
   * fetch next record in result
     *
     * @return
     */
    function movenext(){

    if ($this->debug) echo "Fetching next record  ... ";
      $this->record = @mysql_fetch_array($this->rstemp);
      $status = is_array($this->record);

    if ($this->debug && $status) echo "OK <br>\n\n";
    elseif ($this->debug) echo "EOF <br>\n\n";

      return($status);
  }


  /**
   * mysql::getfield()
   * get field value from the current record
   *
   * @param $field
   * @return
   */
  function getfield($field){

    if ($this->debug) {
      echo "Getting $field ... ";
      //this resource require PHP 4.1 or righter
      if (phpversion() >= 4.1) {
        if (array_key_exists($field,$this->record)) echo "OK <br>\n\n";
        else echo "Not found <br>\n\n";
      } else echo " <br>\n\n";
    }

    return($this->record[$field]);
  }

}

?>
