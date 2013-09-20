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
 * Stock item resource model
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Model_Resource_Stock_Item extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($resource);
    }

    /**
     * Define main table and initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('cataloginventory_stock_item', 'item_id');
    }

    /**
     * Loading stock item data by product
     *
     * @param Magento_CatalogInventory_Model_Stock_Item $item
     * @param int $productId
     * @return Magento_CatalogInventory_Model_Resource_Stock_Item
     */
    public function loadByProductId(Magento_CatalogInventory_Model_Stock_Item $item, $productId)
    {
        $select = $this->_getLoadSelect('product_id', $productId, $item)
            ->where('stock_id = :stock_id');
        $data = $this->_getReadAdapter()->fetchRow($select, array(':stock_id' => $item->getStockId()));
        if ($data) {
            $item->setData($data);
        }
        $this->_afterLoad($item);
        return $this;
    }

    /**
     * Retrieve select object and join it to product entity table to get type ids
     *
     * @param string $field
     * @param mixed $value
     * @param Magento_CatalogInventory_Model_Stock_Item $object
     * @return Magento_DB_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object)
            ->join(array('p' => $this->getTable('catalog_product_entity')),
                'product_id=p.entity_id',
                array('type_id')
            );
        return $select;
    }

    /**
     * Add join for catalog in stock field to product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $productCollection
     * @param array $columns
     * @return Magento_CatalogInventory_Model_Resource_Stock_Item
     */
    public function addCatalogInventoryToProductCollection($productCollection, $columns = null)
    {
        if ($columns === null) {
            $adapter = $this->_getReadAdapter();
            $isManageStock = (int)$this->_coreStoreConfig->getConfig(Magento_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
            $stockExpr = $adapter->getCheckSql('cisi.use_config_manage_stock = 1', $isManageStock, 'cisi.manage_stock');
            $stockExpr = $adapter->getCheckSql("({$stockExpr} = 1)", 'cisi.is_in_stock', '1');

            $columns = array(
                'is_saleable' => new Zend_Db_Expr($stockExpr),
                'inventory_in_stock' => 'is_in_stock'
            );
        }

        $productCollection->joinTable(
            array('cisi' => 'cataloginventory_stock_item'),
            'product_id=entity_id',
            $columns,
            null,
            'left'
        );
        return $this;
    }

    /**
     * Use qty correction for qty column update
     *
     * @param Magento_Core_Model_Abstract $object
     * @param string $table
     * @return array
     */
    protected function _prepareDataForTable(Magento_Object $object, $table)
    {
        $data = parent::_prepareDataForTable($object, $table);
        $ifNullSql = $this->_getWriteAdapter()->getIfNullSql('qty');
        if (!$object->isObjectNew() && $object->getQtyCorrection()) {
            if ($object->getQty() === null) {
                $data['qty'] = null;
            } elseif ($object->getQtyCorrection() < 0) {
                $data['qty'] = new Zend_Db_Expr($ifNullSql . '-' . abs($object->getQtyCorrection()));
            } else {
                $data['qty'] = new Zend_Db_Expr($ifNullSql . '+' . $object->getQtyCorrection());
            }
        }
        return $data;
    }
}
