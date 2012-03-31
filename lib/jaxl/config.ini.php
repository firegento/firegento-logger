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
   * This is one file where you can declare all you username and passwords. You can specify 
   * more than a single username/password/domain and can choose which one to use by specifying
   * the $env variable.
   *
   * On a development enviornment you must be using $env = "devel"
   * On a production enviornment you must be using $env = "prod"
   * You can define your own enviornments as needed
   * ==================================== IMPORTANT =========================================
  */
  
  // Set an enviornment
  $env = "prod";
  
  // Log Level for logger class
  $logEnable = TRUE;
  
  // Log in MySQL database
  $logDB = FALSE;
  
  $key = array("prod"=>array("user"=>"myproductionuser",
                             "pass"=>"password",
                             "host"=>"talk.google.com",
                             "port"=>5222,
                             "domain"=>"gmail.com"
                            ),
              "devel"=>array("user"=>"mydevelopmentuser",
                             "pass"=>"password",
                             "host"=>"localhost",
                             "port"=>5222,
                             "domain"=>"localhost"
                            )
              );
  
  $db = array("prod"=>array("dbuser"=>"root",
                            "dbpass"=>"password",
                            "dbhost"=>"localhost",
                            "dbname"=>"jaxl"
                           ),
             "devel"=>array("dbuser"=>"root",
                            "dbpass"=>"password",
                            "dbhost"=>"localhost",
                            "dbname"=>"jaxl"
                           )
             );
  
?>
