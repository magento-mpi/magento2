<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Di extends Zend\Di\LocatorInterface
{
    /**
     * Get instance manager object
     *
     * @return Magento_Di_InstanceManager
     */
    public function instanceManager();

    /**
     * Retrieve a new instance of a class
     *
     * @param mixed $name
     * @param array $parameters
     * @param bool $isShared
     * @return null|object
     */
    public function newInstance($name, array $parameters = array(), $isShared = true);
}
