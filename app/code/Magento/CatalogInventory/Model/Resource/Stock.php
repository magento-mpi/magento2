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
 * Stock resource model
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Model_Resource_Stock extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Is initialized configuration flag
     *
     * @var boolean
     */
    protected $_isConfig;

    /**
     * Manage Stock flag
     *
     * @var boolean
     */
    protected $_isConfigManageStock;

    /**
     * Backorders
     *
     * @var boolean
     */
    protected $_isConfigBackorders;

    /**
     * Minimum quantity allowed in shopping card
     *
     * @var int
     */
    protected $_configMinQty;

    /**
     * Product types that could have quantities
     *
     * @var array
     */
    protected $_configTypeIds;

    /**
     * Notify for quantity below _configNotifyStockQty value
     *
     * @var int
     */
    protected $_configNotifyStockQty;

    /**
     * Ctalog Inventory Stock instance
     *
     * @var Magento_CatalogInventory_Model_Stock
     */
    protected $_stock;

    /**
     * Catalog inventory data
     *
     * @var Magento_CatalogInventory_Helper_Data
     */
    protected $_catalogInventoryData = null;

    /**
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_Core_Model_Resource $resource
    ) {
        $this->_catalogInventoryData = $catalogInventoryData;
        parent::__construct($resource);
    }

    /**
     * Define main table and initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('cataloginventory_stock', 'stock_id');
    }

    /**
     * Lock product items
     *
     * @param Magento_CatalogInventory_Model_Stock $stock
     * @param int|array $productIds
     * @return Magento_CatalogInventory_Model_Resource_Stock
     */
    public function lockProductItems($stock, $productIds)
    {
        $itemTable = $this->getTable('cataloginventory_stock_item');
        $select = $this->_getWriteAdapter()->select()
            ->from($itemTable)
            ->where('stock_id=?', $stock->getId())
            ->where('product_id IN(?)', $productIds)
            ->forUpdate(true);
        /**
         * We use write adapter for resolving problems with replication
         */
        $this->_getWriteAdapter()->query($select);
        return $this;
    }

    /**
     * Get stock items data for requested products
     *
     * @param Magento_CatalogInventory_Model_Stock $stock
     * @param array $productIds
     * @param bool $lockRows
     * @return array
     */
    public function getProductsStock($stock, $productIds, $lockRows = false)
    {
        if (empty($productIds)) {
            return array();
        }
        $itemTable = $this->getTable('cataloginventory_stock_item');
        $productTable = $this->getTable('catalog_product_entity');
        $select = $this->_getWriteAdapter()->select()
            ->from(array('si' => $itemTable))
            ->join(array('p' => $productTable), 'p.entity_id=si.product_id', array('type_id'))
            ->where('stock_id=?', $stock->getId())
            ->where('product_id IN(?)', $productIds)
            ->forUpdate($lockRows);
        return $this->_getWriteAdapter()->fetchAll($select);
    }

    /**
     * Correct particular stock products qty based on operator
     *
     * @param Magento_CatalogInventory_Model_Stock $stock
     * @param array $productQtys
     * @param string $operator +/-
     * @return Magento_CatalogInventory_Model_Resource_Stock
     */
    public function correctItemsQty($stock, $productQtys, $operator = '-')
    {
        if (empty($productQtys)) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();
        $conditions = array();
        foreach ($productQtys as $productId => $qty) {
            $case = $adapter->quoteInto('?', $productId);
            $result = $adapter->quoteInto("qty{$operator}?", $qty);
            $conditions[$case] = $result;
        }

        $value = $adapter->getCaseSql('product_id', $conditions, 'qty');

        $where = array(
            'product_id IN (?)' => array_keys($productQtys),
            'stock_id = ?'      => $stock->getId()
        );

        $adapter->beginTransaction();
        $adapter->update($this->getTable('cataloginventory_stock_item'), array('qty' => $value), $where);
        $adapter->commit();

        return $this;
    }

    /**
     * add join to select only in stock products
     *
     * @param Magento_Catalog_Model_Resource_Product_Link_Product_Collection $collection
     * @return Magento_CatalogInventory_Model_Resource_Stock
     */
    public function setInStockFilterToCollection($collection)
    {
        $manageStock = Mage::getStoreConfig(Magento_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        $cond = array(
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=1 AND {{table}}.is_in_stock=1',
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=0',
        );

        if ($manageStock) {
            $cond[] = '{{table}}.use_config_manage_stock = 1 AND {{table}}.is_in_stock=1';
        } else {
            $cond[] = '{{table}}.use_config_manage_stock = 1';
        }

        $collection->joinField(
            'inventory_in_stock',
            'cataloginventory_stock_item',
            'is_in_stock',
            'product_id=entity_id',
            '(' . join(') OR (', $cond) . ')'
        );
        return $this;
    }

    /**
     * Load some inventory configuration settings
     *
     */
    protected function _initConfig()
    {
        if (!$this->_isConfig) {
            $configMap = array(
                '_isConfigManageStock'  => Magento_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK,
                '_isConfigBackorders'   => Magento_CatalogInventory_Model_Stock_Item::XML_PATH_BACKORDERS,
                '_configMinQty'         => Magento_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_QTY,
                '_configNotifyStockQty' => Magento_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY
            );

            foreach ($configMap as $field => $const) {
                $this->$field = (int)Mage::getStoreConfig($const);
            }

            $this->_isConfig = true;
            $this->_stock = Mage::getModel('Magento_CatalogInventory_Model_Stock');
            $this->_configTypeIds = array_keys($this->_catalogInventoryData->getIsQtyTypeIds(true));
        }
    }

    /**
     * Set items out of stock basing on their quantities and config settings
     *
     */
    public function updateSetOutOfStock()
    {
        $this->_initConfig();
        $adapter = $this->_getWriteAdapter();
        $values  = array(
            'is_in_stock'                  => 0,
            'stock_status_changed_auto'    => 1
        );

        $select = $adapter->select()
            ->from($this->getTable('catalog_product_entity'), 'entity_id')
            ->where('type_id IN(?)', $this->_configTypeIds);

        $where = sprintf('stock_id = %1$d'
            . ' AND is_in_stock = 1'
            . ' AND ((use_config_manage_stock = 1 AND 1 = %2$d) OR (use_config_manage_stock = 0 AND manage_stock = 1))'
            . ' AND ((use_config_backorders = 1 AND %3$d = %4$d) OR (use_config_backorders = 0 AND backorders = %3$d))'
            . ' AND ((use_config_min_qty = 1 AND qty <= %5$d) OR (use_config_min_qty = 0 AND qty <= min_qty))'
            . ' AND product_id IN (%6$s)',
            $this->_stock->getId(),
            $this->_isConfigManageStock,
            Magento_CatalogInventory_Model_Stock::BACKORDERS_NO,
            $this->_isConfigBackorders,
            $this->_configMinQty,
            $select->assemble()
        );

        $adapter->update($this->getTable('cataloginventory_stock_item'), $values, $where);
    }

    /**
     * Set items in stock basing on their quantities and config settings
     *
     */
    public function updateSetInStock()
    {
        $this->_initConfig();
        $adapter = $this->_getWriteAdapter();
        $values  = array(
            'is_in_stock'   => 1,
        );

        $select = $adapter->select()
            ->from($this->getTable('catalog_product_entity'), 'entity_id')
            ->where('type_id IN(?)', $this->_configTypeIds);

        $where = sprintf('stock_id = %1$d'
            . ' AND is_in_stock = 0'
            . ' AND stock_status_changed_auto = 1'
            . ' AND ((use_config_manage_stock = 1 AND 1 = %2$d) OR (use_config_manage_stock = 0 AND manage_stock = 1))'
            . ' AND ((use_config_min_qty = 1 AND qty > %3$d) OR (use_config_min_qty = 0 AND qty > min_qty))'
            . ' AND product_id IN (%4$s)',
            $this->_stock->getId(),
            $this->_isConfigManageStock,
            $this->_configMinQty,
            $select->assemble()
        );

        $adapter->update($this->getTable('cataloginventory_stock_item'), $values, $where);
    }

    /**
     * Update items low stock date basing on their quantities and config settings
     *
     */
    public function updateLowStockDate()
    {
        $this->_initConfig();

        $adapter = $this->_getWriteAdapter();
        $condition = $adapter->quoteInto('(use_config_notify_stock_qty = 1 AND qty < ?)',
            $this->_configNotifyStockQty) . ' OR (use_config_notify_stock_qty = 0 AND qty < notify_stock_qty)';
        $currentDbTime = $adapter->quoteInto('?', $this->formatDate(true));
        $conditionalDate = $adapter->getCheckSql($condition, $currentDbTime, 'NULL');

        $value  = array(
            'low_stock_date' => new Zend_Db_Expr($conditionalDate),
        );

        $select = $adapter->select()
            ->from($this->getTable('catalog_product_entity'), 'entity_id')
            ->where('type_id IN(?)', $this->_configTypeIds);

        $where = sprintf('stock_id = %1$d'
            . ' AND ((use_config_manage_stock = 1 AND 1 = %2$d) OR (use_config_manage_stock = 0 AND manage_stock = 1))'
            . ' AND product_id IN (%3$s)',
            $this->_stock->getId(),
            $this->_isConfigManageStock,
            $select->assemble()
        );

        $adapter->update($this->getTable('cataloginventory_stock_item'), $value, $where);
    }

    /**
     * Add low stock filter to product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     * @param array $fields
     * @return Magento_CatalogInventory_Model_Resource_Stock
     */
    public function addLowStockFilter(Magento_Catalog_Model_Resource_Product_Collection $collection, $fields)
    {
        $this->_initConfig();
        $adapter = $collection->getSelect()->getAdapter();
        $qtyIf = $adapter->getCheckSql(
            'invtr.use_config_notify_stock_qty > 0',
            $this->_configNotifyStockQty,
            'invtr.notify_stock_qty'
        );
        $conditions = array(
            array(
                $adapter->prepareSqlCondition('invtr.use_config_manage_stock', 1),
                $adapter->prepareSqlCondition($this->_isConfigManageStock, 1),
                $adapter->prepareSqlCondition('invtr.qty', array('lt' => $qtyIf))
            ),
            array(
                $adapter->prepareSqlCondition('invtr.use_config_manage_stock', 0),
                $adapter->prepareSqlCondition('invtr.manage_stock', 1)
            )
        );

        $where = array();
        foreach ($conditions as $k => $part) {
            $where[$k] = join(' ' . Zend_Db_Select::SQL_AND . ' ', $part);
        }

        $where = $adapter->prepareSqlCondition('invtr.low_stock_date', array('notnull' => true))
            . ' ' . Zend_Db_Select::SQL_AND . ' (('
            .  join(') ' . Zend_Db_Select::SQL_OR .' (', $where)
            . '))';

        $collection->joinTable(array('invtr' => 'cataloginventory_stock_item'),
            'product_id = entity_id',
            $fields,
            $where
        );
        return $this;
    }
}
