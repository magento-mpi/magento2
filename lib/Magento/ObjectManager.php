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
     * @return mixed
     */
    public function create($className, array $arguments = array());

    /**
     * Retreive cached object instance
     *
     * @abstract
     * @param string $className
     * @param array $arguments
     * @return mixed
     */
    public function get($className, array $arguments = array());
}
