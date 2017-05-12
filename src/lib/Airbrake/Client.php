<?php
namespace Airbrake;

use Exception;

require_once realpath(__DIR__.'/Record.php');
require_once realpath(__DIR__.'/Configuration.php');
require_once realpath(__DIR__.'/Connection.php');
require_once realpath(__DIR__.'/Version.php');
require_once realpath(__DIR__.'/Exception.php');
require_once realpath(__DIR__.'/Notice.php');
require_once realpath(__DIR__.'/Resque/NotifyJob.php');

/**
 * Airbrake client class.
 *
 * @package    Airbrake
 * @author     Drew Butler <drew@dbtlr.com>
 * @copyright  (c) 2011-2013 Drew Butler
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Client
{
    protected $configuration = null;
    protected $connection = null;
    protected $notice = null;

    /**
     * Build the Client with the Airbrake Configuration.
     *
     * @throws Airbrake\Exception
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $configuration->verify();

        $this->configuration = $configuration;
        $this->connection    = new Connection($configuration);
    }

    /**
     * Notify on an error message.
     *
     * @param string $message
     * @param array $backtrace
     * @return string
     */
    public function notifyOnError($message, array $backtrace = null)
    {
        if (!$backtrace) {
            $backtrace = debug_backtrace();
            if (count($backtrace) > 1) {
                array_shift($backtrace);
            }
        }

        $notice = new Notice;
        $notice->load(array(
            'errorClass'   => 'PHP Error',
            'backtrace'    => $backtrace,
            'errorMessage' => $message,
        ));

        return $this->notify($notice);
    }

    /**
     * Notify on an exception
     *
     * @param Airbrake\Notice $notice
     * @return string
     */
    public function notifyOnException(Exception $exception)
    {
        $notice = new Notice;
        $notice->load(array(
            'errorClass'   => get_class($exception),
            'backtrace'    => $this->cleanBacktrace($exception->getTrace() ?: debug_backtrace()),
            'errorMessage' => $exception->getMessage().' in '.$exception->getFile().' on line '.$exception->getLine(),
        ));

        return $this->notify($notice);
    }

    /**
     * Notify about the notice.
     *
     * If there is a PHP Resque client given in the configuration, then use that to queue up a job to
     * send this out later. This should help speed up operations.
     *
     * @param Airbrake\Notice $notice
     */
    public function notify(Notice $notice)
    {
        if ($this->configuration->queue && class_exists('Resque')) {
            $data = array('notice' => serialize($notice), 'configuration' => serialize($this->configuration));
            \Resque::enqueue($this->configuration->queue, 'Airbrake\\Resque\\NotifyJob', $data);
            return;
        }

        return $this->connection->send($notice);
    }

    /**
     * Clean the backtrace of unneeded junk.
     *
     * @param array $backtrace
     * @return array
     */
    protected function cleanBacktrace($backtrace)
    {
        foreach ($backtrace as &$item) {
            unset($item['args']);
        }

        return $backtrace;
    }
}