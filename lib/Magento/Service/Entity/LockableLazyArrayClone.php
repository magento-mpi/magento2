<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\Entity;

/**
 * Class allows to clone array values of object/array type lazily, when accessed
 *
 * This class will also allow for locking that prevents modifications of the data until cloned.
 */
class LockableLazyArrayClone extends LazyArrayClone implements LockableInterface
{
    /**
     * @var bool Indicates if the object is locked for modifications or not.
     */
    protected $_locked = false;

    /**
     * Clones the object and unlocks it if it was locked.
     */
    public function __clone()
    {
        parent::__clone();
        $this->_locked = false;
    }

    /**
     * @inheritdoc
     */
    public function lock()
    {
        if ($this->_locked) {
            return;
        }
        $this->_locked = true;

        foreach ($this as $key => $value) {
            if ($value instanceof LockableInterface) {
                $value->lock();
            } else if (is_array($value) || ($value instanceof \ArrayAccess)) {
                $lazyClone = new LockableLazyArrayClone();
                foreach ($value as $subKey => $subValue) {
                    $lazyClone[$subKey] = $subValue;
                }
                $lazyClone->lock();
                parent::offsetSet($key, $lazyClone);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function isLocked()
    {
        return $this->_locked;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->_validateUnlocked();
        parent::offsetSet($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->_validateUnlocked();
        parent::offsetUnset($offset);
    }

    /**
     * Makes sure the object isn't locked.
     *
     * If it is locked then an exception will be thrown to stop any further processing.
     *
     * @throws \LogicException thrown when object is locked.
     */
    private function _validateUnlocked()
    {
        if ($this->_locked) {
            throw new \LogicException(
                'This object is locked for modification.'
                . " To obtain an unlocked copy, please 'clone' this object, as the resulting copy will be unlocked."
            );
        }
    }
}
