<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_ObjectManager_Factory
{
    /**
     * @abstract
     * @param array $arguments
     * @return mixed
     */
    public function createFromArray(array $arguments = array());
}
