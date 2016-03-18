<?php
namespace Airbrake;

use Exception;

require_once 'Client.php';
require_once 'Configuration.php';

/**
 * Airbrake EventHandler class.
 *
 * @package    Airbrake
 * @author     Drew Butler <drew@dbtlr.com>
 * @copyright  (c) 2011-2013 Drew Butler
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class EventHandler
{
    /**
     * The singleton instance
     */
    protected static $instance = null;
    protected $airbrakeClient  = null;
    protected $notifyOnWarning = null;

    protected $warningErrors = array(\E_NOTICE            => 'Notice',
                                     \E_STRICT            => 'Strict',
                                     \E_USER_WARNING      => 'User Warning',
                                     \E_USER_NOTICE       => 'User Notice',
                                     \E_DEPRECATED        => 'Deprecated',
                                     \E_WARNING           => 'Warning',
                                     \E_USER_DEPRECATED   => 'User Deprecated',
                                     \E_CORE_WARNING      => 'Core Warning',
                                     \E_COMPILE_WARNING   => 'Compile Warning',
                                     \E_RECOVERABLE_ERROR => 'Recoverable Error' );

    protected $fatalErrors = array(\E_ERROR             => 'Error',
                                   \E_PARSE             => 'Parse',
                                   \E_COMPILE_ERROR     => 'Compile Error',
                                   \E_CORE_ERROR        => 'Core Error',
                                   \E_USER_ERROR        => 'User Error' );

    /**
     * Build with the Airbrake client class.
     *
     * @param Airbrake\Client $client
     */
    public function __construct(Client $client, $notifyOnWarning)
    {
        $this->notifyOnWarning = $notifyOnWarning;
        $this->airbrakeClient = $client;
    }

    /**
     * Get the current handler.
     *
     * @param string $apiKey
     * @param bool $notifyOnWarning
     * @param array $options
     * @return EventHandler
     */
    public static function start($apiKey, $notifyOnWarning=false, array $options=array())
    {
        if ( !isset(self::$instance)) {
            $config = new Configuration($apiKey, $options);

            $client = new Client($config);
            self::$instance = new self($client, $notifyOnWarning);

            set_error_handler(array(self::$instance, 'onError'));
            set_exception_handler(array(self::$instance, 'onException'));
            register_shutdown_function(array(self::$instance, 'onShutdown'));
        }

        return self::$instance;
    }


    /**
     * Revert the handlers back to their original state.
     */
    public static function reset()
    {
        if (isset(self::$instance)) {
            restore_error_handler();
            restore_exception_handler();
        }

        self::$instance = null;
    }

    /**
     * Catches standard PHP style errors
     *
     * @see http://us3.php.net/manual/en/function.set-error-handler.php
     * @param int $type
     * @param string $message
     * @param string $file
     * @param string $line
     * @param array $context
     * @return bool
     */
    public function onError($type, $message, $file = null, $line = null, $context = null)
    {
        // This will catch silenced @ function calls and keep them quiet.
        if (ini_get('error_reporting') == 0) {
            return true;
        }

        if (isset($this->fatalErrors[$type])) {
            throw new Exception($message);
        }

        if ($this->notifyOnWarning && isset ($this->warningErrors[$type])) {
            // Make sure we pass in the current backtrace, minus this function call.
            $backtrace = debug_backtrace();
            array_shift($backtrace);

            $this->airbrakeClient->notifyOnError($message, $backtrace);
            return true;
        }

        return true;
    }


    /**
     * Catches uncaught exceptions.
     *
     * @see http://us3.php.net/manual/en/function.set-exception-handler.php
     * @param Exception $exception
     * @return bool
     */
    public function onException(Exception $exception)
    {
        $this->airbrakeClient->notifyOnException($exception);

        return true;
    }

    /**
     * Handles the PHP shutdown event.
     *
     * This event exists almost soley to provide a means to catch and log errors that might have been
     * otherwise lost when PHP decided to die unexpectedly.
     */
    public function onShutdown()
    {
        // If the instance was unset, then we shouldn't run.
        if (self::$instance == null) {
            return;
        }

        // This will help prevent multiple calls to this, incase the shutdown handler was declared
        // multiple times. This only should occur in unit tests, when the handlers are created
        // and removed repeatedly. As we cannot remove shutdown handlers, this prevents us from
        // calling it 1000 times at the end.
        self::$instance = null;

        // Get the last error if there was one, if not, let's get out of here.
        if (!$error = error_get_last()) {
            return;
        }

        // Don't notify on warning if not configured to.
        if (!$this->notifyOnWarning && isset($this->warningErrors[$error['type']])) {
            return;
        }

        // Build a fake backtrace, so we at least can show where we came from.
        $backtrace = array(
            array(
                'file' => $error['file'],
                'line' => $error['line'],
                'function' => '',
                'args' => array(),
            )
        );

        $this->airbrakeClient->notifyOnError('[Improper Shutdown] '.$error['message'], $backtrace);
    }
}
