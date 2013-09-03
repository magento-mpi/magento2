<?php
/**
 * Factory for Magento_Core_Model_DataService_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_DataService_Config_Reader_Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new Magento_Core_Model_DataService_Config_Reader by array of configuration files
     *
     * @param array $configFiles
     * @return Magento_Core_Model_DataService_Config_Reader
     */
    public function createReader(array $configFiles)
    {
        return $this->_objectManager->create('Magento_Core_Model_DataService_Config_Reader',
            array('configFiles'  => $configFiles));
    }
}