<?php
/**
 * Class allows to clone array values of object/array type lazily, when accesses
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

class LazyArrayClone implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    private $_container = [];

    /**
     * @var bool tracks whether or not this object is a clone.
     */
    private $_clone = false;

    /**
     * @var array stores an array of object hashes for objects that have already been cloned.
     */
    private $_clonedObjectHashes = [];

    /**
     * Set cloned flag to true
     */
    public function __clone()
    {
        $this->_clone = true;
    }

    /**
     *@inheritdoc
     */
    public function getIterator()
    {
        return new ArrayAccessIterator($this, array_keys($this->_container));
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->_container[$offset]);
    }

    /**
     * Returns the value at the specified offset
     *
     * Performes lazy cloning on read for the values of object or array type
     *
     * @param mixed $offset
     * @return mixed
     */
    public function &offsetGet($offset)
    {
        if (!$this->_clone || !(is_array($this->_container[$offset]) || is_object($this->_container[$offset]))) {
            return $this->_container[$offset];
        }

        if ($this->_clone && is_object($this->_container[$offset])
            && !$this->_isClone($this->_container[$offset])
        ) {
            $this->_container[$offset] = $this->_cloneObject($this->_container[$offset]);
        }

        if ($this->_clone && is_array($this->_container[$offset])) {
            foreach ($this->_container[$offset] as $key => $value) {
                if (is_object($value) && !$this->_isClone($value)) {
                    $this->_container[$offset][$key] = $this->_cloneObject($value);
                }
            }
        }

        return $this->_container[$offset];
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_container[] = $value;
        } else {
            $this->_container[$offset] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->_container[$offset]);
    }

    /**
     * Clones an object and returns the clone.
     *
     * This will also register the clone in our list of cloned objects
     *
     * @param mixed $object
     * @return mixed
     */
    private function _cloneObject($object)
    {
        $clonedCopy = clone $object;

        $this->_clonedObjectHashes[spl_object_hash($clonedCopy)] = true;

        return $clonedCopy;
    }

    /**
     * Checks whether we have already cloned an object or not.
     *
     * @param $object
     * @return bool true if the object has already been cloned
     */
    private function _isClone($object)
    {
        return isset($this->_clonedObjectHashes[spl_object_hash($object)]);
    }

    /**
     * Return data as array
     *
     * @return array
     */
    public function __toArray()
    {
        $result = $this->_container;
        foreach ($result as $key => $value) {
            if ($value instanceof LazyArrayClone) {
                $result[$key] = $value->__toArray();
            }
        }
        return $result;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->_container);
    }
}
