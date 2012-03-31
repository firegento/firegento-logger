<?php
  
  /* Include Key file */
  include_once("config.ini.php");
  
  /* Include JAXL Class */
  include_once("jaxl.class.php");
  
  /* Create an instance of XMPP Class */
  $jaxl = new JAXL($key[$env]['host'],    // Jabber Server Hostname
                   $key[$env]['port'],    // Jabber Server Port
                   $key[$env]['user'],    // Jabber User
                   $key[$env]['pass'],    // Jabber Password
                   $key[$env]['domain'],  // Jabber Domain
                   $db[$env]['dbhost'],   // MySQL DB Host
                   $db[$env]['dbname'],   // MySQL DB Name
                   $db[$env]['dbuser'],   // MySQL DB User
                   $db[$env]['dbpass'],   // MySQL DB Pass
                   $logEnable,            // Enable Logging
                   $logDB                 // Enable MySQL Logging
                  );
  
  try {
    /* Initiate the connection */
    $jaxl->connect();
    
    /* Communicate with Jabber Server */
    while($jaxl->isConnected) {
      $jaxl->getXML();
    }
  }
  catch(Exception $e) {
    die($e->getMessage());
  }

?>
