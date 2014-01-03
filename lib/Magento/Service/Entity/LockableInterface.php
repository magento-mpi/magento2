<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\Entity;

/**
 * Interface LockableInterface adds locking capabilities to an object
 */
interface LockableInterface
{
    /**
     * Locks this object to prevent any further modifications.
     *
     * Clone a locked object to receive an unlocked copy.
     *
     * A locked object should throw a \LogicException when an attempt is made to modify it.
     */
    public function lock();

    /**
     * Can be used to check if the object has already been locked.
     *
     * @return bool true if the object is locked for modification
     */
    public function isLocked();
}
