<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Directory Resource Setup Model
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Directory_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryData;

    /**
     * @param Magento_Directory_Helper_Data $directoryData
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config_Modules $modulesConfig
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param $resourceName
     */
    public function __construct(
        Magento_Directory_Helper_Data $directoryData,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config_Modules $modulesConfig,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        $resourceName
    ) {
        parent::__construct($resourcesConfig, $modulesConfig, $moduleList, $resource, $modulesReader, $resourceName);
        $this->_directoryData = $directoryData;
    }

    /**
     * @return Magento_Directory_Helper_Data
     */
    public function getDirectoryData()
    {
        return $this->_directoryData;
    }
}
