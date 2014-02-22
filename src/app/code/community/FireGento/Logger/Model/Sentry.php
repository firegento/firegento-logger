<?php

set_include_path(get_include_path() . PATH_SEPARATOR . 'lib' . DS . 'raven-php' . DS . 'lib' . DS);

class FireGento_Logger_Model_Sentry extends Zend_Log_Writer_Abstract
{
    /**
     * @var array
     */
    protected $_options = array();

    /**
     * sentry client
     *
     * @var Raven_Client
     */
    protected $_sentryClient;

    protected $_priorityToLevelMapping
        = array(
            0 => 'fatal',
            1 => 'fatal',
            2 => 'fatal',
            3 => 'error',
            4 => 'warning',
            5 => 'info',
            6 => 'info',
            7 => 'debug'
        );


    /**
     *
     *
     * ignore filename - it is Zend_Log_Writer_Abstract dependency
     *
     * @param string $filename
     *
     * @return \Hackathon_LoggerSentry_Model_Sentry
     */
    public function __construct($filename)
    {
        /* @var $helper Hackathon_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');
        $options = array(
            'logger' => $helper->getLoggerConfig('sentry/logger_name')
        );
        $this->_sentryClient = new Raven_Client($helper->getLoggerConfig('sentry/apikey'), $options);
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * @param array $event Event data
     *
     * @throws Zend_Log_Exception
     * @return void
     */
    protected function _write($event)
    {
        try {
            /* @var $helper Hackathon_Logger_Helper_Data */
            $helper = Mage::helper('firegento_logger');
            $helper->addEventMetadata($event);

            $additional = array(

                'file' => $event['file'],
                'line' => $event['line'],
            );

            foreach (array('REQUEST_METHOD', 'REQUEST_URI', 'REMOTE_IP', 'HTTP_USER_AGENT') as $key) {
                if (!empty($event[$key])) {
                    $additional[$key] = $event[$key];
                }
            }

            $this->_sentryClient->captureMessage(
                $event['message'], array(), $this->_priorityToLevelMapping[$event['priority']], true, $additional
            );

        } catch (Exception $e) {
            throw new Zend_Log_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Satisfy newer Zend Framework
     *
     * @static
     *
     * @param $config
     *
     * @return void
     */
    static public function factory($config)
    {
    }
}