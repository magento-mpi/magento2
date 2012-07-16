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
     * @param string $objectName
     * @param array $arguments
     * @return mixed
     */
    public function create($objectName, array $arguments = array());

    /**
     * Retreive cached object instance
     *
     * @abstract
     * @param string $objectName
     * @param array $arguments
     * @return mixed
     */
    public function get($objectName, array $arguments = array());
}
