<?php
/**
Copyright (c) 2013 Diego Zanella (http://dev.pathtoenlightenment.net)

@package Remote SysLog
@author Diego Zanella <diego@pathtoenlightenment.net>
@copyright Copyright (c) 2013 Diego Zanella (http://dev.pathtoenlightenment.net)
@license http://dev.pathtoenlightenment.net/noncommercial-licence/ Noncommercial Licence
*/

/**
 * Encapsulates a Syslog Message.
 */
class SyslogMessage {
	// @const Maximum length of a Syslog message, as specified by RFC3164
	const MAX_MESSAGE_LENGTH = 1024;
	// @const Maximum length of the Tag section of a message
	const MAX_TAG_LENGTH = 1024;
	const DEFAULT_PROCESSNAME = 'PHP';
	const DEFAULT_HOSTNAME = 'NONAME';

  protected $Facility; // @link SyslogFacility
  protected $Severity; // @link SyslogSeverity
  protected $HostName; // no embedded space, no domain name, only a-z A-Z 0-9 and other authorized characters
  protected $FQDN;
  protected $IPAddress;
  protected $ProcessName;
  protected $Message;
	protected $Timestamp;

	/**
	 * Returns the first non null value amongst the arguments.
	 *
	 * @return The first non null value amongst the arguments.
	 */
	protected function Coalesce() {
		$Args = func_get_args();
		foreach ($Args as $Arg) {
			if (!empty($Arg)) {
				return $Arg;
			}
		}
		return null;
	}

	/**
	 * Returns the Host Name.
	 *
	 * @return The Host Name of the machine where the script is running, or a
	 * default value if the name cannot be retrieved.
	 */
	protected function GetHostName() {
		if(empty($this->HostName)) {
			$ComputerName = empty($_ENV['COMPUTERNAME']) ? null : $_ENV['COMPUTERNAME'];
			$HostName = empty($_ENV['HOSTNAME']) ? null : $_ENV['HOSTNAME'];

			$HostNameParts = explode('.', $this->Coalesce($ComputerName,
																										$HostName,
																										self::DEFAULT_HOSTNAME));
			$this->HostName = $HostNameParts[0];
		}
		return $this->HostName;
	}

	/**
	 * Returns the Fully Qualified Domain Name of the Server.
	 *
	 * @return Fully Qualified Domain Name of the Server, or an empty string if the
	 * name could not be resolved.
	 */
	protected function GetFQDN() {
		if(empty($this->FQDN)) {
			$this->FQDN = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : '';
		}

		return $this->FQDN;
	}

	/**
	 * Returns the IP Address of the Server.
	 *
	 * @return The IP Address of the Server, or an empty string if the address
	 * could not be retrieved.
	 */
	protected function GetIPAddress() {
		if(empty($this->IPAddress)) {
			$this->IPAddress = isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : '';
		}

		return $this->IPAddress;
	}

	/**
	 * Returns the Process Name.
	 *
	 * @return The Name of the Process that is running the script, or a default
	 * value if the name cannot be retrieved.
	 */
	protected function GetProcessName() {
		return isset($this->ProcessName) ? $this->ProcessName : self::DEFAULT_PROCESSNAME;
	}

	/**
	 * Sets several options for the instance.
	 *
	 * @param Options An array of options that will be used to configure the
	 * instance.
	 */
	protected function SetOptions(array $Options) {
		$this->HostName = $Options['HostName'];
		$this->FQDN = $Options['FQDN'];
		$this->SetProcessName($Options['ProcessName']);
	}

	/**
	 * Setter for Facility property.
	 */
  public function SetFacility($Facility) {
		if(!SyslogFacility::IsValidFacility($Facility)) {
			throw new InvalidArgumentException('Invalid Facility. Facility value must be in range 0-23.');
		}
    $this->Facility = $Facility;
  }

	/**
	 * Setter for Severity property.
	 */
  public function SetSeverity($Severity) {
		if(!SyslogSeverity::IsValidSeverity($Severity)) {
			throw new InvalidArgumentException('Invalid Severity. Severity value must be in range 0-7.');
		}

    $this->Severity = $Severity;
  }

	/**
	 * Setter for ProcessName property.
	 */
  public function SetProcessName($ProcessName) {
		// Process Name can have a maximum length of 32 characters
    $this->ProcessName = $ProcessName;
  }

	/**
	 * Setter for Message property.
	 */
  public function SetMessage($Message) {
    $this->Message = $Message;
  }

	/**
	 * Setter for Timestamp property.
	 *
	 * @link http://tools.ietf.org/html/rfc5424
	 */
	public function SetTimestamp($Timestamp) {
		if(!is_numeric($Timestamp)) {
			throw new Exception('Timestamp must be a number.');
		}
		$this->Timestamp = $Timestamp;
	}

  /**
	 * Class constructor.
	 *
	 * @param Message The log message.
	 * @param Facility The Facility that generated the message (@link SyslogFacility).
	 * @param Severity The Severity of the message (@link SyslogSeverity).
	 * @param Timestamp The timestamp of the log message.
	 * @param Options Additional configuration options (@link Options).
	 */
	public function __construct($Message, $Facility = 16, $Severity = 5, $Timestamp, $Options = null) {
		$this->SetMessage($Message);
    $this->SetFacility($Facility);
    $this->SetSeverity($Severity);
		$this->SetTimestamp($Timestamp);

		if(isset($Options)) {
			$this->SetOptions($Options);
		}
	}

	/**
	 * Returns a Timestamp string, formatted as required by RFC3164.
	 *
	 * @return A Timestamp string, formatted as required by RFC3164.
	 */
	private function GetSyslogTimestamp() {
		// It's a bit messy to format the timestamp as required by RFC3164, because
		// the Day has to be padded with spaces and date() doesn't allow it. For this
		// reason, the Timestamp is first formatted as "Month %2s Hours:Minutes:Seconds",
		// then this string is used with sprintf to pad the day returned by the second
		// date() statement.
		return sprintf(date('M %2\s H:i:s', $this->Timestamp), date('j', $this->Timestamp));
	}

	/**
	 * Calculates and returns the Priority of a Log Message.
	 *
	 * @return A value indicating the Priority of the Log Message.
	 */
	private function GetPriority() {
    return sprintf('<%d>', $this->Facility * 8 + $this->Severity);
	}

	/**
	 * Builds the TAG part of the Syslog Message.
	 *
	 * @return The TAG part of the Syslog Message, as specified by RFC3164.
	 */
	private function GetTag() {
		$Tag = sprintf('%s%s %s ', $this->GetPriority(),
															$this->GetSyslogTimestamp(),
															$this->GetHostName());
		return substr($Tag, 0, self::MAX_TAG_LENGTH);
	}

	/**
	 * Generates a unique ID for the message. This function is used only if the
	 * message is too long and has to be split into chunks; the ID generated by
	 * this method can be used to trace the pieces and put them back together.
	 *
	 * @return A unique ID for the Message, in the form of a prefix and an MD5
	 * .hash
	 */
	private function GetMessageID() {
		return sprintf('MSG_%s', md5(uniqid('', true)));
	}

	/**
	 * Puts all Log Message elements together to form a string that will be passed
	 * to the RSysLog Server.
	 *
	 * @return string The Message as a string.
	 */
	protected function FormatMessage() {
		return sprintf('%s %s [PID: %d] %s',
									 $this->GetFQDN(),
									 $this->GetProcessName(),
									 getmypid(),
									 $this->Message);
	}

	/**
	 * Returns the chunks that form the full log message. If the message is short
	 * enough, it's not split and just returned.
	 *
	 * @return An array containing the chunk of the Log Message. If the message is
	 * short enough, the array will contain only one value.
	 */
	public function GetMessageChunks() {
		$MessageTag = $this->GetTag();
		$MessageText = $this->FormatMessage();

		// If the Tag+Message string is short enough to fit in a single message,
		// simply return it
		if(strlen($MessageTag . $MessageText) < self::MAX_MESSAGE_LENGTH) {
			return array(sprintf('%s:%s', $MessageTag, $MessageText));
		}

		// If Message is too long, split it. To make it easier to put the pieces
		// together, append a Unique ID to each piece of the message
		$MessageUniqueID = $this->GetMessageID();
		$MaxChunkLength = self::MAX_MESSAGE_LENGTH - strlen($this->GetTag() . $MessageUniqueID);
		// Split the message in chunks, so that each one is small enough to be sent
		// with the header
		$Chunks = str_split($MessageText, $MaxChunkLength);

		$Result = array();
		// Build a full message for each chunk, adding Tag and Message ID
		foreach($Chunks as $Chunk) {
			$Result[] = sprintf('%s:%s [Message ID: %s]',
													$MessageTag,
													$Chunk,
													$MessageUniqueID);
		}
		return $Result;
	}
}
