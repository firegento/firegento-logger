<?php
  
  /*
   * Author:
   *    Abhinav Singh
   *
   * Contact:
   *    mailsforabhinav@gmail.com
   *    admin@abhinavsingh.com
   *
   * Site:
   *    http://abhinavsingh.com
   *    http://abhinavsingh.com/blog
   *
   * Source:
   *    http://code.google.com/p/jaxl
   *
   * About:
   *    JAXL stands for "Just Another XMPP Library"
   *    For geeks, JAXL stands for "Jabber XMPP Library"
   *    
   *    I wrote this library while developing Gtalkbots (http://gtalkbots.com)
   *    I have highly customized it to work with Gtalk Servers and inspite of
   *    production level usage at Gtalkbots, I recommend still not to use this
   *    for any live project.
   *    
   *    Feel free to add me in Gtalk and drop an IM.
   *
  */
  
  /*
   * ==================================== IMPORTANT =========================================
   * This is a basic MySQL connection class, which can be used to log received messages and
   * presence in MySQL database. Connection parameters are defined in config.ini.php
   *
   * Available methods:
   *    __construct(), __destruct()
   *    getData(), setData()
   * ==================================== IMPORTANT =========================================
  */
  
  class MySQL {
    
    /*
     * __construct() method performs various initialization
    */
    function __construct($dbhost,$dbname,$dbuser,$dbpass) {
      $this->dbhost = $dbhost;
      $this->dbname = $dbname;
      $this->dbuser = $dbuser;
      $this->dbpass = $dbpass;
      
      $this->db = NULL;
      
      $this->connect();
    }
    
    /*
     * __destruct() method closes down the connection to MySQL
    */
    function __destruct() {
      $this->close();
    }
    
    /*
     * connect() method establish connection to MySQL database
    */
    function connect() {
      $connection = mysql_connect($this->dbhost,$this->dbuser,$this->dbpass) or die(mysql_error());
      if(!$connection) {
        return FALSE;
      }
      else {
        $database = @mysql_select_db($this->dbname);
        if(!$database) {
          return FALSE;
        }
        else {
          $this->db = $connection;
        }
      }
    }
    
    /*
     * close() method shuts down connection to MySQL database
    */
    function close() {
      mysql_close($this->db);
    }
    
    /*
     * getData() method executes the passed $query
     * Used for SELECT queries
     *
     * This method provides data back in two styles
     * 1. Original (Not Recommended)
     * 2. Array (Recommended)
    */
    function getData($query, $options = "array") {
      $result = mysql_query($query,$this->db);
      if($result) {
        if($options['type'] == "original") {
          return $result;
        }
        else if($options['type'] == "array") {
          // Return the associative array and number of rows
          $mysql_num_rows = mysql_num_rows($result);
          $result_arr = array();
          while($info = mysql_fetch_assoc($result)) {
            array_push($result_arr,$info);
          }
          $resultset = array("mysql_num_rows" => $mysql_num_rows,"result" => $result_arr,"false_query" => "no");
          return $resultset;
        }
        mysql_free_result($result);
      }
      else {
        if($options['type'] == "array") {
          $resultset = array("false_query" => "yes");
          return $resultset;
        }
      }
    }
  
    /*
     * setData() method executes the passed $query
     * Used for INSERT and UPDATE queries
    */
     function setData($query) {
      $result = mysql_query($query,$this->db);
      return array('result'=>$result,'mysql_affected_rows'=>mysql_affected_rows());
    }
  }
?>
