<?php
namespace Airbrake;

require_once 'Record.php';

use Airbrake\Exception as AirbrakeException;

/**
 * Airbrake configuration class.
 *
 * Loads via the inherited Record class methods.
 *
 * @package    Airbrake
 * @author     Drew Butler <drew@dbtlr.com>
 * @copyright  (c) 2011-2013 Drew Butler
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Configuration extends Record
{
    protected $_apiKey;
    protected $_timeout = 2;
    protected $_environmentName = 'production';
    protected $_serverData;
    protected $_getData;
    protected $_postData;
    protected $_sessionData;
    protected $_component;
    protected $_action;
    protected $_projectRoot;
    protected $_url;
    protected $_hostname;
    protected $_queue;
    protected $_secure = false;
    protected $_host = 'api.airbrake.io';
    protected $_resource = '/notifier_api/v2/notices';
    protected $_apiEndPoint;

    /**
     * Load the given data array to the record.
     *
     * @param string $apiKey
     * @param array|stdClass $data
     */
    public function __construct($apiKey, $data = array())
    {
        $data['apiKey'] = $apiKey;
        parent::__construct($data);
    }

    /**
     * Initialize the data source.
     */
    protected function initialize()
    {
        if (!$this->serverData) {
            $this->serverData = (array) $_SERVER;
        }

        if (!$this->getData) {
            $this->getData = (array) $_GET;
        }

        if (!$this->postData) {
            $this->postData = (array) $_POST;
        }

        if (!$this->sessionData && isset($_SESSION)) {
            $this->sessionData = (array) $_SESSION;
        }

        if (!$this->projectRoot) {
            $this->projectRoot = isset($this->serverData['_']) ? $this->serverData['_'] : $this->serverData['DOCUMENT_ROOT'];
        }

        if (!$this->url) {
            $this->url = isset($this->serverData['REDIRECT_URL']) ? $this->serverData['REDIRECT_URL'] : $this->serverData['SCRIPT_NAME'];
        }

        if (!$this->hostname) {
            $this->hostname = isset($this->serverData['HTTP_HOST']) ? $this->serverData['HTTP_HOST'] : 'No Host';
        }

        $protocol = $this->secure ? 'https' : 'http';
        $this->apiEndPoint = $this->apiEndPoint ?: $protocol.'://'.$this->host.$this->resource;
    }

    /**
     * Get the combined server parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return array_merge($this->get('postData'), $this->get('getData'));
    }

    /**
     * Verify that the configuration is complete.
     */
    public function verify()
    {
        if (!$this->apiKey) {
            throw new AirbrakeException('Cannot initialize the Airbrake client without an ApiKey being set to the configuration.');
        }
    }
}
