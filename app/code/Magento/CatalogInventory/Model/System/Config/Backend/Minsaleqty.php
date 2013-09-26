<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend for serialized array data
 *
 */
class Magento_CatalogInventory_Model_System_Config_Backend_Minsaleqty extends Magento_Core_Model_Config_Value
{
    /**
     * Catalog inventory minsaleqty
     *
     * @var Magento_CatalogInventory_Helper_Minsaleqty
     */
    protected $_catalogInventoryMinsaleqty = null;

    /**
     * @param Magento_CatalogInventory_Helper_Minsaleqty $catalogInventoryMinsaleqty
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_CatalogInventory_Helper_Minsaleqty $catalogInventoryMinsaleqty,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogInventoryMinsaleqty = $catalogInventoryMinsaleqty;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Process data after load
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = $this->_catalogInventoryMinsaleqty->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $value = $this->_catalogInventoryMinsaleqty->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }
}
