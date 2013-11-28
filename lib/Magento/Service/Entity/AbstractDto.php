<?php
/**
 * Initialize and provide access to LazyArrayClone internal storage. Ensure it is cloned on clone operation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

abstract class AbstractDto implements MagentoDtoInterface
{
    /**
     * @var LockableLazyArrayClone Stores all data for this DTO
     */
    protected $_data;

    /**
     * Initialize internal storage
     */
    public function __construct()
    {
        $this->_data = new LockableLazyArrayClone();
    }

    /**
     * @inheritdoc
     */
    public function __clone()
    {
        $this->_validateDataType();
        $this->_data = clone $this->_data;
    }

    /**
     * @inheritdoc
     */
    public function isLocked()
    {
        $this->_validateDataType();
        return $this->_data->isLocked();
    }

    /**
     * @inheritdoc
     */
    public function lock()
    {
        $this->_validateDataType();
        $this->_data->lock();
    }

    /**
     * @inheritdoc
     */
    public function __toArray()
    {
        return $this->_data->__toArray();
    }

    /**
     * Recommended method for creating arrays for storing inside of the DTO.
     *
     * @return LazyArrayClone
     */
    protected function _createArray()
    {
        return new LockableLazyArrayClone();
    }

    /**
     * Retrieves a value from the data array if set, or null otherwise.
     *
     * @param string $key
     * @return mixed|null
     */
    protected function _get($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return null;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return AbstractDto
     */
    protected function _set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return AbstractDto
     */
    protected function _setData($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * Validates that $data is the proper type
     *
     * @throws \LogicException
     */
    private function _validateDataType()
    {
        if (null === $this->_data
            || !($this->_data instanceof \ArrayAccess)
            || !($this->_data instanceof LockableInterface)) {
            throw new \LogicException('Unable to clone because $_data is not a lockable array access object. '
                . ' Please be sure to call parent::__construct when extending AbstractDto.');
        }
    }
}
