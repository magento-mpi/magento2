<?php
/**
 * Class allows iterating over an ArrayAccess object
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

class ArrayAccessIterator implements \Iterator
{
    /** @var \ArrayAccess  */
    private $_arrayAccess;

    /** @var string[]  */
    private $_keys;

    /** @var int current position in the $_keys array */
    private $_current = 0;

    /**
     * @param \ArrayAccess $arrayAccessObject
     * @param string[] $keys List of keys in the ArrayAccess object, can be null if not a map.
     */
    public function __construct($arrayAccessObject, $keys = null)
    {
        $this->_arrayAccess = $arrayAccessObject;
        $this->_keys = $keys;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->_arrayAccess->offsetGet($this->key());
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->_current++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        if ($this->_keys === null) {
            $key = $this->_current;
        } else {
            $key = isset($this->_keys[$this->_current]) ? $this->_keys[$this->_current] : null;
        }
        return $key;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        $key = $this->key();
        if (null === $key) {
            return false;
        }
        return $this->_arrayAccess->offsetExists($key);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->_current = 0;
    }
}
