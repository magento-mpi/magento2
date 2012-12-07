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
 * Helper class for modules.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Module extends Mage_PHPUnit_Helper_Abstract
{
    /**
     * Returns if a module is disabled
     *
     * @param string $moduleName
     * @return bool
     */
    public function isModuleDisabled($moduleName)
    {
        $moduleConfig = Mage::app()->getConfig()->getModuleConfig($moduleName);
        return $moduleConfig && !$moduleConfig->is('active');
    }

    /**
     * Returns module name by the class name in it
     *
     * @param string|object $class
     * @return string
     */
    public function getModuleNameByClass($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        return strtok($class, '_') . '_' . strtok('_');
    }
}
