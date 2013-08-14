<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Core_Model_Config_ModulesInterface extends Magento_Core_Model_ConfigInterface
{
    /**
     * Get module config node
     *
     * @param string $moduleName
     * @return Magento_Simplexml_Element
     */
    public function getModuleConfig($moduleName = '');
}
