<?php
namespace Airbrake;

use ArrayAccess, IteratorAggregate, ArrayIterator;

/**
 * A record abstract that can help accelerate building models.
 *
 * The extended object simply needs to define what the properties of the object
 * are going to be. An example of this is as follows:
 *
 * <pre>
 * class Person extends Record
 * {
 *     protected $_FirstName;
 *     protected $_LastName;
 *     protected $_Age;
 * }
 * </pre>
 *
 * Now you can simply retrieve these properties by requesting them by their key name
 * minus the prefixed '_'. So, if you were to call ->get( 'FirstName' ) it would
 * retrieve that key for you. Similarly, you can call set( 'FirstName', 'Drew' ) and
 * it will set that key. Give load() an array or stdClass of key value pairs and it
 * will parse those into their matching keys. Any key that is given that does not
 * exist in the parameters will be ignored.
 *
 * These objects may also be accessed and iterated over as if they were arrays. This means
 * that if you prefer the $obj['key'] syntax, you are free to use it. Any keys that are set
 * to it that are not known to the record type will be ignored and any that do not exist
 * when getting, will simply return null.
 *
 * @package    Airbrake
 * @author     Drew Butler <drew@dbtlr.com>
 * @copyright  (c) 2011-2013 Drew Butler
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
abstract class Record implements ArrayAccess, IteratorAggregate
{
    const PREFIX = '_';


    /**
     * Load the given data array to the record.
     *
     * @param array|stdClass $data
     */
    public function __construct($data = array())
    {
        $this->load($data);
        $this->initialize();
    }

    /**
     * Get the value for the given key.
     *
     * The given key should match one of the parameters about, but with out the
     * prefix. That is added on during this process.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if ($this->exists($key)) {
            $key = self::PREFIX.$key;
            return $this->$key;
        }

        return null;
    }

    /**
     * Magic alias for the get() method.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set the given value to the given key.
     *
     * The given key should match one of the parameters about, but with out the
     * prefix. That is added on during this process.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        if ($this->exists($key)) {
            $key = self::PREFIX.$key;
            $this->$key = $value;
        }
    }

    /**
     * Magic alias for the set() method.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Load the given data array to the record.
     *
     * @param array|stdClass $data
     */
    public function load($data)
    {
        if (!is_array($data) && !$data instanceof \stdClass) {
            return;
        }

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Dump the data into an array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = array();
        $vars = get_object_vars($this);

        foreach ($vars as $key => $value) {
            if ($key[0] === self::PREFIX) {
                $key = substr($key, 1, strlen($key) - 1);

                if ($value instanceof Record) {
                    $value = $value->toArray();
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Is the given key set in this record?
     *
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        return property_exists($this, self::PREFIX.$key);
    }

    /**
     * Get the keys that are contained in this record.
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->toArray());
    }

    /**
     * Set the given value for the given key
     *
     * Part of the ArrayAccess interface.
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        if (!is_null($key)) {
            $this->set($key, $value);
        }
    }

    /**
     * Is the given key available?
     *
     * Part of the ArrayAccess interface.
     *
     * @return string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->exists($key);
    }

    /**
     * Set the given key to null
     *
     * Part of the ArrayAccess interface.
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }

    /**
     * Get the value for the given key.
     *
     * Part of the ArrayAccess interface.
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Get the iterator for the IteratorAggregate interface.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->dump());
    }

    /**
     * Optional method to declare that will initialize the data on construct.
     */
    protected function initialize() {}
}