<?php
/**
Copyright (c) 2013 Diego Zanella (http://dev.pathtoenlightenment.net)

@package Remote SysLog
@author Diego Zanella <diego@pathtoenlightenment.net>
@copyright Copyright (c) 2013 Diego Zanella (http://dev.pathtoenlightenment.net)
@license http://dev.pathtoenlightenment.net/noncommercial-licence/ Noncommercial Licence
*/

require_once('syslogfacility.php');
require_once('syslogseverity.php');
require_once('syslogmessage.php');

/**
 * Sends SysLog messages to a Remote Server.
 */
class RSyslog {
	// @var Syslog destination server.
  private $LogServer;
	// @var Port to use for communication. Standard syslog port is 514.
  private $Port = 514;
	// @var Timeout of the UDP connection, in seconds.
  private $Timeout;

	/**
	 * Class constructor.
	 *
	 * @param LogServer The Name or IP Address of the remote Log Server. It can be
	 * indicated in format <server>[:<port>].
	 * @param Timeout The timeout for the UDP connection, in seconds.
	 */
  public function __construct($LogServer, $Timeout = 1) {
		$this->SetLogServer($LogServer);
		$this->SetTimeout($Timeout);
  }

	/**
	 * Setter for LogServer property.
	 */
  function SetLogServer($LogServer) {
		if(empty($LogServer)) {
			return;
		}

		// LogServer can be in format <server>:<port>
		$LogServerParts = explode(':', $LogServer);

		$this->LogServer = $LogServerParts[0];
		$this->SetPort($LogServerParts[1]);
  }

	/**
	 * Setter for Port property.
	 */
  function SetPort($Port) {
    if(((int)$Port > 0) && ((int)$Port < 65536)) {
			$this->Port = (int)$Port;
    }
  }

	/**
	 * Setter for Timeout property.
	 */
  function SetTimeout($Timeout) {
    if((int)$Timeout > 0) {
			$this->Timeout = (int)$Timeout;
    }
  }

	/**
	 * Sends a Message to the remote Log Server.
	 *
	 * @param Message An instance of SyslogMessage class.
	 * @param LogServer The Server to which the message will be sent. If omitted,
	 * the one specified when the class was instantiated will be used instead. It
	 * can be indicated as <server>[:<port>].
	 * @param Timeout Timeout for the UDP Connection, in seconds. If omitted,
	 * the one specified when the class was instantiated will be used instead.
	 * @return True if the message was sent correctly. If not, an array containing
	 * an Error Code and an Error Message.
	 */
  function Send(SyslogMessage $Message, $LogServer = null, $Timeout = null) {
    $this->SetLogServer($LogServer);
		$this->SetTimeout($Timeout);

    $Socket = fsockopen(sprintf('udp://%s', $this->LogServer), $this->Port, $ErrorNumber, $ErrorMessage);
    if ($Socket) {
			foreach($Message->GetMessageChunks() as $MessageChunk) {
		    fwrite($Socket, $MessageChunk);
			}
	    fclose($Socket);
	    return true;
    }
    else {
			return array($ErrorNumber, $ErrorMessage);
    }
  }
}

//$Rsyslog = new RSyslog('logs.papertrailapp.com:22426');
