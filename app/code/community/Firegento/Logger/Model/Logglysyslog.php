<?php
require_once 'lib/rsyslog/rsyslog.php';

class Firegento_Logger_Model_Logglysyslog extends Firegento_Logger_Model_Rsyslog
{
    // @var int Default UDP Port for JSON Remote Syslog on Loggly
    const DEFAULT_PORT = 42146;

    /**
     * Transforms a Magento Log event into an associative array.
     *
     * @param array event A Magento Log Event.
     * @param bool enableBacktrace Indicates if a backtrace should be added to the
     * log event.
     * @return array An associative array representation of the event.
     */
    protected function BuildJSONMessage($event, $enableBacktrace = FALSE)
    {
    Mage::helper('firegento_logger')->addEventMetadata($event, '-', $enableBacktrace);

        $Fields = array();

        $Fields['Level'] = $event['priority'];
        $Fields['FileName'] = $event['file'];
        $Fields['LineNumber'] = $event['line'];
        $Fields['StoreCode'] = $event['store_code'];
        $Fields['TimeElapsed'] = $event['time_elapsed'];
        $Fields['Host'] = php_uname('n');
        $Fields['TimeStamp'] = date('Y-m-d H:i:s', strtotime($event['timestamp']));
        $Fields['Facility'] = $this->_options['AppName'] . $this->_options['FileName'];

        if ($event['backtrace']) {
            $Fields['Message'] = $event['message']."\n\nBacktrace:\n".$event['backtrace'];
        } else {
            $Fields['Message'] = $event['message'];
        }

        foreach (array('REQUEST_METHOD', 'REQUEST_URI', 'REMOTE_IP', 'HTTP_USER_AGENT') as $Key) {
            if (!empty($event[$Key])) {
                $Fields[$Key] = $event[$Key];
            }
        }

        return $Fields;
    }

    /**
     * Builds a Message that will be sent to a RSyslog Server.
     *
     * @param array event A Magento Log Event.
     * @return string A string representing the message.
     */
    protected function BuildSysLogMessage($event)
    {
        return new LogglySyslogMessage($this->BuildJSONMessage($event, $this->_enableBacktrace),
                                                                     self::DEFAULT_FACILITY,
                                                                     $event['priority'],
                                                                     strtotime($event['timestamp']));
    }

    /**
     * @param  string                              $FileName
     * @return Firegento_Logger_Model_Logglysyslog
     */
    public function __construct($FileName)
    {
        $helper = Mage::helper('firegento_logger'); /* @var $helper Firegento_Logger_Helper_Data */
        $this->_options['FileName'] = basename($FileName);
        $this->_options['AppName'] = $helper->getLoggerConfig('logglysyslog/app_name');

        $this->HostName = $helper->getLoggerConfig('logglysyslog/hostname');
        $this->Port = $helper->getLoggerConfig('logglysyslog/port');
        $this->Timeout = $helper->getLoggerConfig('logglysyslog/timeout');
    }
}

/**
 * Implementation of Remote Syslog Message for Loggly. This class logs the
 * events using JSON, which allows providing more details than basic Syslog.
 *
 * @see SyslogMessage.
 */
class LogglySyslogMessage extends SyslogMessage
{
    public function __construct($Message, $Facility = 16, $Severity = 5, $Timestamp, $Options = null)
    {
        parent::__construct($Message, $Facility, $Severity, $Timestamp, $Options);
    }

    /**
     * Puts all Log Message elements together to form a JSON String that will be
     * passed to the RSysLog Server.
     *
     * @return string The Message as a JSON object.
     */
    protected function FormatMessage()
    {
        $this->Message['FQDN'] = $this->GetFQDN();
        $this->Message['ProcessName'] = $this->GetProcessName();
        $this->Message['PID'] = getmypid();

        return json_encode($this->Message);
    }

    /**
     * Returns the chunks of the message to send to the RSysLog server.
     * Note: this specific implementation sends messages as whole JSON Objects,
     * there are no "chunks".
     *
     * @return string A JSON representation of the Log message.
     */
    public function GetMessageChunks()
    {
        return(array($this->FormatMessage()));
    }

}
