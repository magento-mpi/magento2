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
 * Catalog Inventory Manage Stock Config Backend Model
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Model_Config_Backend_Managestock
    extends Magento_Core_Model_Config_Value
{
    /**
     * @var Magento_CatalogInventory_Model_Stock_Status
     */
    protected $_stockStatus;

    /**
     * @param Magento_CatalogInventory_Model_Stock_Status $stockStatus
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_CatalogInventory_Model_Stock_Status $stockStatus,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_stockStatus = $stockStatus;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After change Catalog Inventory Manage value process
     *
     * @return Magento_CatalogInventory_Model_Config_Backend_Managestock
     */
    protected function _afterSave()
    {
        $oldValue = $this->_coreConfig->getValue(
            Magento_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($this->getValue() != $oldValue) {
            $this->_stockStatus->rebuild();
        }

        return $this;
    }
}
