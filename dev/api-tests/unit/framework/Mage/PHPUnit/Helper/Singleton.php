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
        // Fix for new data helper code behavior
        if (false !== strpos($registryKey, '/data')) {
            $helperKey = substr($registryKey, 0, -5);

            Mage::unregister($helperKey);
            Mage::register($helperKey, $object);
        }

        Mage::unregister($registryKey);
        Mage::register($registryKey, $object);
    }
}
