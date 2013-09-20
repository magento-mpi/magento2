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
namespace Magento\TestFramework\Helper;

class Config
{
    /**
     * Returns enabled modules in the system
     *
     * @return array
     */
    public function getEnabledModules()
    {
        $result = array();
        $moduleList = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\ModuleListInterface');
        foreach ($moduleList->getModules() as $module) {
            $result[] = $module['name'];
        }
        return $result;
    }
}
