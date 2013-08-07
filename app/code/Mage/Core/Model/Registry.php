<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Registry model. Used to manage values in registry
 */
class Mage_Core_Model_Registry
{
    /**
     * Retrieve a value from registry by a key
     *
     * @param string $key
     * @return mixed
     */
    public function registry($key)
    {
        return Mage::registry($key);
    }

    /**
     * Register a new variable
     *
     * @param string $key
     * @param mixed $value
     * @param bool $graceful
     * @throws Mage_Core_Exception
     */
    public function register($key, $value, $graceful = false)
    {
        Mage::register($key, $value, $graceful);
    }

    /**
     * Unregister a variable from register by key
     *
     * @param string $key
     */
    public function unregister($key)
    {
        Mage::unregister($key);
    }
}
