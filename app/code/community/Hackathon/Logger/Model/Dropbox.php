<?php
class Hackathon_Logger_Model_Dropbox extends Zend_Log_Writer_Abstract
{
    private $filename = null;

    public function __construct($filename)
    {
        // Set your consumer key, secret and callback URL
        $key      = '???';
        $secret   = '????';

        // Check whether to use HTTPS and set the callback URL
        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        // Instantiate the required Dropbox objects
        $encrypter = new \Dropbox\OAuth\Storage\Encrypter('????');
        $storage = new \Dropbox\OAuth\Storage\Session($encrypter);
        $OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
        $dropbox = new \Dropbox\API($OAuth);

        $this->filename = mt_rand().$filename;

    }

    public function _write($event)
    {
        //Lazy intatiation of underlying mailer
        parent::_write($event);

    }

    /**
     * Satisfy newer Zend Framework
     *
     * @static
     * @param $config
     */
    static public function factory($config) {}

}
