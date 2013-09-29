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
namespace Magento\Reports\Model\Resource\Product\Lowstock;

class Collection extends \Magento\Reports\Model\Resource\Product\Collection
{
    /**
     * CatalogInventory Stock Item Resource instance
     *
     * @var \Magento\CatalogInventory\Model\Resource\Stock\Item
     */
    protected $_inventoryItemResource      = null;

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
     * @var \Magento\CatalogInventory\Helper\Data
     */
    protected $_inventoryData = null;

    /**
     * @param \Magento\CatalogInventory\Helper\Data $catalogInventoryData
     * @param \Magento\Catalog\Helper\Product\Flat $catalogProductFlat
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Catalog\Model\Resource\Product $product
     */
    public function __construct(
        \Magento\CatalogInventory\Helper\Data $catalogInventoryData,
        \Magento\Catalog\Helper\Product\Flat $catalogProductFlat,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Catalog\Model\Resource\Product $product
    ) {
        $this->_inventoryData = $catalogInventoryData;
        parent::__construct(
            $catalogProductFlat, $catalogData, $eventManager, $logger,
            $fetchStrategy, $coreStoreConfig, $entityFactory, $product
        );
    }

    /**
     * Retrieve CatalogInventory Stock Item Resource instance
     *
     * @return \Magento\CatalogInventory\Model\Resource\Stock\Item
     */
    protected function _getInventoryItemResource()
    {
        if ($this->_inventoryItemResource === null) {
            $this->_inventoryItemResource = \Mage::getResourceSingleton(
                    'Magento\CatalogInventory\Model\Resource\Stock\Item'
                );
        }
        return $this->_inventoryItemResource;
    }

    /**
     * Retrieve CatalogInventory Stock Item Table
     *
     * @return string
     */
    protected function _getInventoryItemTable()
    {
        return $this->_getInventoryItemResource()->getMainTable();
    }

    /**
     * Retrieve CatalogInventory Stock Item Table Id field name
     *
     * @return string
     */
    protected function _getInventoryItemIdField()
    {
        return $this->_getInventoryItemResource()->getIdFieldName();
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
     * @return \Magento\Reports\Model\Resource\Product\Lowstock\Collection
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
     * @return \Magento\Reports\Model\Resource\Product\Lowstock\Collection
     */
    public function filterByProductType($typeFilter)
    {
        if (!is_string($typeFilter) && !is_array($typeFilter)) {
            \Mage::throwException(
                __('The product type filter specified is incorrect.')
            );
        }
        $this->addAttributeToFilter('type_id', $typeFilter);
        return $this;
    }

    /**
     * Add filter by product types from config
     * Only types witch has QTY parameter
     *
     * @return \Magento\Reports\Model\Resource\Product\Lowstock\Collection
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
     * @return \Magento\Reports\Model\Resource\Product\Lowstock\Collection
     */
    public function useManageStockFilter($storeId = null)
    {
        $this->joinInventoryItem();
        $manageStockExpr = $this->getConnection()->getCheckSql(
            $this->_getInventoryItemField('use_config_manage_stock') . ' = 1',
            (int) $this->_coreStoreConfig->getConfig(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_MANAGE_STOCK, $storeId),
            $this->_getInventoryItemField('manage_stock')
        );
        $this->getSelect()->where($manageStockExpr . ' = ?', 1);
        return $this;
    }

    /**
     * Add Notify Stock Qty Condition to collection
     *
     * @param int $storeId
     * @return \Magento\Reports\Model\Resource\Product\Lowstock\Collection
     */
    public function useNotifyStockQtyFilter($storeId = null)
    {
        $this->joinInventoryItem(array('qty'));
        $notifyStockExpr = $this->getConnection()->getCheckSql(
            $this->_getInventoryItemField('use_config_notify_stock_qty') . ' = 1',
            (int)$this->_coreStoreConfig->getConfig(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_NOTIFY_STOCK_QTY, $storeId),
            $this->_getInventoryItemField('notify_stock_qty')
        );
        $this->getSelect()->where('qty < ?', $notifyStockExpr);
        return $this;
    }
}
