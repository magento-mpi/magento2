<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stock item collection resource model
 */
class Magento_CatalogInventory_Model_Resource_Stock_Item_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store model manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);

        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CatalogInventory_Model_Stock_Item', 'Magento_CatalogInventory_Model_Resource_Stock_Item');
    }

    /**
     * Add stock filter to collection
     *
     * @param mixed $stock
     * @return Magento_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    public function addStockFilter($stock)
    {
        if ($stock instanceof Magento_CatalogInventory_Model_Stock) {
            $this->addFieldToFilter('main_table.stock_id', $stock->getId());
        } else {
            $this->addFieldToFilter('main_table.stock_id', $stock);
        }
        return $this;
    }

    /**
     * Add product filter to collection
     *
     * @param array $products
     * @return Magento_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    public function addProductsFilter($products)
    {
        $productIds = array();
        foreach ($products as $product) {
            if ($product instanceof Magento_Catalog_Model_Product) {
                $productIds[] = $product->getId();
            } else {
                $productIds[] = $product;
            }
        }
        if (empty($productIds)) {
            $productIds[] = false;
            $this->_setIsLoaded(true);
        }
        $this->addFieldToFilter('main_table.product_id', array('in' => $productIds));
        return $this;
    }

    /**
     * Join Stock Status to collection
     *
     * @param int $storeId
     * @return Magento_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    public function joinStockStatus($storeId = null)
    {
        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
        $this->getSelect()->joinLeft(
            array('status_table' => $this->getTable('cataloginventory_stock_status')),
                'main_table.product_id=status_table.product_id'
                . ' AND main_table.stock_id=status_table.stock_id'
                . $this->getConnection()->quoteInto(' AND status_table.website_id=?', $websiteId),
            array('stock_status')
        );

        return $this;
    }

    /**
     * Add Managed Stock products filter to collection
     *
     * @param boolean $isStockManagedInConfig
     * @return Magento_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    public function addManagedFilter($isStockManagedInConfig)
    {
        if ($isStockManagedInConfig) {
            $this->getSelect()->where('(manage_stock = 1 OR use_config_manage_stock = 1)');
        } else {
            $this->addFieldToFilter('manage_stock', 1);
        }

        return $this;
    }

    /**
     * Add filter by quantity to collection
     *
     * @param string $comparisonMethod
     * @param float $qty
     * @return Magento_CatalogInventory_Model_Resource_Stock_Item_Collection
     * @throws Magento_Core_Exception
     */
    public function addQtyFilter($comparisonMethod, $qty)
    {
        $methods = array(
            '<'  => 'lt',
            '>'  => 'gt',
            '='  => 'eq',
            '<=' => 'lteq',
            '>=' => 'gteq',
            '<>' => 'neq'
        );
        if (!isset($methods[$comparisonMethod])) {
            throw new Magento_Core_Exception(__('%1 is not a correct comparison method.', $comparisonMethod));
        }

        return $this->addFieldToFilter('main_table.qty', array($methods[$comparisonMethod] => $qty));
    }

    /**
     * Initialize select object
     *
     * @return Magento_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    protected function _initSelect()
    {
        return parent::_initSelect()->getSelect()
            ->join(
                array('cp_table' => $this->getTable('catalog_product_entity')),
                'main_table.product_id = cp_table.entity_id',
                array('type_id')
            );
    }
}
