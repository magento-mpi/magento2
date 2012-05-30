<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class for singletons in Mage::registry.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Singleton extends Mage_PHPUnit_Helper_Abstract
{
    /**
     * Registers singleton object in the Mage::registry().
     *
     * @param string $registryKey
     * @param mixed $object
     */
    public function registerSingleton($registryKey, $object)
    {
        Mage::unregister($registryKey);
        Mage::register($registryKey, $object);
    }
}
