<?php
/**
 * {license_notice}
 * 
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper with routines to work with Magento config
 */
class Magento_Test_Helper_Config
{
    /**
     * Returns enabled modules in the system
     *
     * @return array
     */
    public function getEnabledModules()
    {
        $result = array();
        $moduleList = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Mage_Core_Model_ModuleListInterface');
        foreach ($moduleList->getModules() as $module) {
            $result[] = $module['name'];
        }
        return $result;
    }
}
