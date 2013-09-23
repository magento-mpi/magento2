<?php
/**
 * High-level interface for catalog attributes data that hides format from the client code
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Attribute_Config
{
    /**
     * @var Magento_Catalog_Model_Attribute_Config_Data
     */
    protected $_dataStorage;

    /**
     * @param Magento_Catalog_Model_Attribute_Config_Data $dataStorage
     */
    public function __construct(Magento_Catalog_Model_Attribute_Config_Data $dataStorage)
    {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Retrieve names of attributes belonging to specified group
     *
     * @param string $groupName Name of an attribute group
     * @return array
     */
    public function getAttributeNames($groupName)
    {
        $data = $this->_dataStorage->getData();
        return isset($data[$groupName]) ? $data[$groupName] : array();
    }
}
