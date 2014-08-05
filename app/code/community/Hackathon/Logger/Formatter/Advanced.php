<?php
/**
 * @author Colin Mollenhour
 */
class Hackathon_Logger_Formatter_Advanced extends Zend_Log_Formatter_Simple
{

    /**
     * @param null|string $format
     */
    public function __construct($format = NULL)
    {
        $configFormat = Mage::helper('hackathon_logger')->getLoggerConfig('general/format');
        if ($configFormat) {
            $format = str_replace('\n', PHP_EOL, $configFormat);
        }
        if ( ! $format) {
            $format = self::DEFAULT_FORMAT;
        }
        parent::__construct($format . PHP_EOL);
    }

    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param array $event
     * @param bool $enableBacktrace
     * @return string             formatted line to write to the log
     */
    public function format($event, $enableBacktrace = FALSE)
    {
        Mage::helper('hackathon_logger')->addEventMetadata($event, '-', $enableBacktrace);
        $output = preg_replace_callback('/%(\w+)%/', function ($match) use ($event) {
            $value = isset($event[$match[1]]) ? $event[$match[1]] : '-';
            if (is_string($value) || (is_object($value) && method_exists($value, '__toString'))) {
                return "$value";
            } else if (is_array($value)) {
                return substr(@json_encode($value, JSON_PRETTY_PRINT), 0, 1000);
            } else {
                return gettype($value);
            }
        }, $this->_format);
        return $output;
    }

}
