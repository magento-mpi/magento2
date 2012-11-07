<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_ObjectManager
{
    /**
     * Create new object instance
     *
     * @abstract
     * @param string $className
     * @param array $arguments
     * @param bool $isShared
     * @return mixed
     */
    public function create($className, array $arguments = array(), $isShared = true);

    /**
     * Retrieve cached object instance
     *
     * @abstract
     * @param string $className
     * @param array $arguments
     * @return mixed
     */
    public function get($className, array $arguments = array());

    /**
     * Load DI configuration for specified ares
     *
     * @abstract
     * @param string $areaCode
     * @return mixed
     */
    public function loadAreaConfiguration($areaCode = null);
}
