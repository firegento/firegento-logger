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
   * This is a very basic logger class. It helps in logging each and every XML being send or
   * received from the Jabber Server.
   *
   * TO-DO: Enable Log rotation based on time and size.
   * ==================================== IMPORTANT =========================================
  */
  
  class Logger {
    
    var $availableOptions = array("size","time");
    var $rotationBasedOn = "size";
    var $logFileName = "log/logger.log";
    var $maxLogSize = 1024;
    
    /*
     * logger() method function which logs in all XML sent or received
    */
    function logger($log) {
      $fh = fopen($this->logFileName,"a");
      fwrite($fh,date('Y-m-d H:i:s')."\n".$log."\n\n");
      fclose($fh);
    }
    
  }
  
?>
