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
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param array $moduleConfiguration
     * @param string $resourceName
     */
    public function __construct(
        Magento_Directory_Helper_Data $directoryData,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        array $moduleConfiguration,
        $resourceName
    ) {
        $this->_directoryData = $directoryData;
        parent::__construct($logger, $eventManager, $resource, $modulesReader, $moduleConfiguration, $resourceName);
    }

    /**
     * @return Magento_Directory_Helper_Data
     */
    public function getDirectoryData()
    {
        return $this->_directoryData;
    }
}
