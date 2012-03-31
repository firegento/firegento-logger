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
   * JAXL extends XMPP and should be the starting point for all your applications
   * You should never try to change XMPP class until you are confident about it
   *
   * Methods you might be interested in:
   *    eventMessage(), eventPresence()
   *    sendMessage($jid,$message), sendStatus($status)
   *    subscribe($jid)
   *    roster('get')
   *    roster('add',$jid) 
   *    roster('remove',$jid)
   *    roster('update',$jid,$name,$groups)
   * ==================================== IMPORTANT =========================================
  */
  
  /* Include XMPP Class */
  include_once("xmpp.class.php");
  
  class JAXL extends XMPP {
    
    function eventMessage($fromJid, $content, $offline = FALSE) {
      if($offline) {
        $this->sendMessage($fromJid,"Hi, Thanks for your offline message");
      }
      else {
        $this->sendMessage($fromJid,"Hi, Thanks for your message");
      }
      
      if($this->logDB) {
        // Save the message in the database
        $timestamp = date('Y-m-d H:i:s');
        $query = "INSERT INTO message (FromJid,Message,Timestamp) value ('$fromJid','$content','$timestamp')";
        $this->mysql->setData($query);
      }
    }
    
    function eventPresence($fromJid, $status, $photo) {
      // Change your status message to your friend's status
      // $this->sendStatus($status);
      
      if($this->logDB) {
        // Save the presence in the database
        $timestamp = date('Y-m-d H:i:s');
        $query = "INSERT INTO presence (FromJid,Status,Timestamp) value ('$fromJid','$status','$timestamp')";
        $this->mysql->setData($query);
      }
    }
    
    function setStatus() {
      // Set a custom status or use $this->status
      $this->sendStatus($this->status);
      print "Setting Status...\n";
      print "Done\n";
    }
    
  }
  
?>
