<?php
/**
 * Factory for Mage_Core_Model_DataService_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_Config_Reader_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return new Mage_Core_Model_DataService_Config_Reader
     *
     * @param array $arguments
     * @return Mage_Core_Model_DataService_Config_Reader
     */
    public function createReader(array $arguments = array())
    {
        return $this->_objectManager->create('Mage_Core_Model_DataService_Config_Reader', $arguments);
    }
}