<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product Low Stock Report Collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Product_Lowstock_Collection extends Magento_Reports_Model_Resource_Product_Collection
{
    /**
     * Flag about is joined CatalogInventory Stock Item
     *
     * @var bool
     */
    protected $_inventoryItemJoined        = false;

    /**
     * Alias for CatalogInventory Stock Item Table
     *
     * @var string
     */
    protected $_inventoryItemTableAlias    = 'lowstock_inventory_item';

    /**
     * Catalog inventory data
     *
     * @var Magento_CatalogInventory_Helper_Data
     */
    protected $_inventoryData = null;

    /**
     * @var Magento_CatalogInventory_Model_Resource_Stock_Item
     */
    protected $_itemResource;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Resource $coreResource
     * @param Magento_Eav_Model_EntityFactory $eavEntityFactory
     * @param Magento_Eav_Model_Resource_Helper $resourceHelper
     * @param Magento_Validator_UniversalFactory $universalFactory
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Catalog_Model_Resource_Product $product
     * @param Magento_Reports_Model_Event_TypeFactory $eventTypeFactory
     * @param Magento_Catalog_Model_Product_Type $productType
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_CatalogInventory_Model_Resource_Stock_Item $itemResource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Resource $coreResource,
        Magento_Eav_Model_EntityFactory $eavEntityFactory,
        Magento_Eav_Model_Resource_Helper $resourceHelper,
        Magento_Validator_UniversalFactory $universalFactory,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Catalog_Model_Resource_Product $product,
        Magento_Reports_Model_Event_TypeFactory $eventTypeFactory,
        Magento_Catalog_Model_Product_Type $productType,
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_CatalogInventory_Model_Resource_Stock_Item $itemResource
    )
    {
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $eavConfig, $coreResource,
            $eavEntityFactory, $resourceHelper, $universalFactory, $catalogData, $catalogProductFlat, $coreStoreConfig,
            $product, $eventTypeFactory, $productType
        );
        $this->_inventoryData = $catalogInventoryData;
        $this->_itemResource = $itemResource;
    }

    /**
     * Retrieve CatalogInventory Stock Item Table
     *
     * @return string
     */
    protected function _getInventoryItemTable()
    {
        return $this->_itemResource->getMainTable();
    }

    /**
     * Retrieve CatalogInventory Stock Item Table Id field name
     *
     * @return string
     */
    protected function _getInventoryItemIdField()
    {
        return $this->_itemResource->getIdFieldName();
    }

    /**
     * Retrieve alias for CatalogInventory Stock Item Table
     *
     * @return string
     */
    protected function _getInventoryItemTableAlias()
    {
        return $this->_inventoryItemTableAlias;
    }

    /**
     * Add catalog inventory stock item field to select
     *
     * @param string $field
     * @param string $alias
     * @return Magento_Reports_Model_Resource_Product_Lowstock_Collection
     */
    protected function _addInventoryItemFieldToSelect($field, $alias = null)
    {
        if (empty($alias)) {
            $alias = $field;
        }

        if (isset($this->_joinFields[$alias])) {
            return $this;
        }

        $this->_joinFields[$alias] = array(
            'table' => $this->_getInventoryItemTableAlias(),
            'field' => $field
        );

        $this->getSelect()->columns(array($alias => $field), $this->_getInventoryItemTableAlias());
        return $this;
    }

    /**
     * Retrieve catalog inventory stock item field correlation name
     *
     * @param string $field
     * @return string
     */
    protected function _getInventoryItemField($field)
    {
        return sprintf('%s.%s', $this->_getInventoryItemTableAlias(), $field);
    }

    /**
     * Join catalog inventory stock item table for further stock_item values filters
     *
     * @param array $fields
     * @return $this
     */
    public function joinInventoryItem($fields = array())
    {
        if (!$this->_inventoryItemJoined) {
            $this->getSelect()->join(
                array($this->_getInventoryItemTableAlias() => $this->_getInventoryItemTable()),
                sprintf('e.%s = %s.product_id',
                    $this->getEntity()->getEntityIdField(),
                    $this->_getInventoryItemTableAlias()
                ),
                array()
            );
            $this->_inventoryItemJoined = true;
        }

        if (!is_array($fields)) {
            if (empty($fields)) {
                $fields = array();
            } else {
                $fields = array($fields);
            }
        }

        foreach ($fields as $alias => $field) {
            if (!is_string($alias)) {
                $alias = null;
            }
            $this->_addInventoryItemFieldToSelect($field, $alias);
        }

        return $this;
    }

    /**
     * Add filter by product type(s)
     *
     * @param array|string $typeFilter
     * @return Magento_Reports_Model_Resource_Product_Lowstock_Collection
     */
    public function filterByProductType($typeFilter)
    {
        if (!is_string($typeFilter) && !is_array($typeFilter)) {
            new Magento_Core_Exception(__('The product type filter specified is incorrect.'));
        }
        $this->addAttributeToFilter('type_id', $typeFilter);
        return $this;
    }

    /**
     * Add filter by product types from config
     * Only types witch has QTY parameter
     *
     * @return Magento_Reports_Model_Resource_Product_Lowstock_Collection
     */
    public function filterByIsQtyProductTypes()
    {
        $this->filterByProductType(
            array_keys(array_filter($this->_inventoryData->getIsQtyTypeIds()))
        );
        return $this;
    }

    /**
     * Add Use Manage Stock Condition to collection
     *
     * @param int|null $storeId
     * @return Magento_Reports_Model_Resource_Product_Lowstock_Collection
     */
    public function useManageStockFilter($storeId = null)
    {
        $this->joinInventoryItem();
        $manageStockExpr = $this->getConnection()->getCheckSql(
            $this->_getInventoryItemField('use_config_manage_stock') . ' = 1',
            (int) $this->_coreStoreConfig->getConfig(Magento_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK, $storeId),
            $this->_getInventoryItemField('manage_stock')
        );
        $this->getSelect()->where($manageStockExpr . ' = ?', 1);
        return $this;
    }

    /**
     * Add Notify Stock Qty Condition to collection
     *
     * @param int $storeId
     * @return Magento_Reports_Model_Resource_Product_Lowstock_Collection
     */
    public function useNotifyStockQtyFilter($storeId = null)
    {
        $this->joinInventoryItem(array('qty'));
        $notifyStockExpr = $this->getConnection()->getCheckSql(
            $this->_getInventoryItemField('use_config_notify_stock_qty') . ' = 1',
            (int)$this->_coreStoreConfig->getConfig(Magento_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY, $storeId),
            $this->_getInventoryItemField('notify_stock_qty')
        );
        $this->getSelect()->where('qty < ?', $notifyStockExpr);
        return $this;
    }
}
