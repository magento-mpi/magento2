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
namespace Magento\CatalogInventory\Model\Resource\Stock\Item;

use Magento\CatalogInventory\Model\Stock;

class Collection extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Zend_Db_Adapter_Abstract $connection
     * @param \Magento\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\CatalogInventory\Model\Stock\Item',
            'Magento\CatalogInventory\Model\Resource\Stock\Item'
        );
    }

    /**
     * Add stock filter to collection
     *
     * @param Stock|string|array $stock
     * @return $this
     */
    public function addStockFilter($stock)
    {
        if ($stock instanceof Stock) {
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
     * @return $this
     */
    public function addProductsFilter($products)
    {
        $productIds = array();
        foreach ($products as $product) {
            if ($product instanceof \Magento\Catalog\Model\Product) {
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
     * @return $this
     */
    public function joinStockStatus($storeId = null)
    {
        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
        $this->getSelect()->joinLeft(
            array('status_table' => $this->getTable('cataloginventory_stock_status')),
            'main_table.product_id=status_table.product_id' .
            ' AND main_table.stock_id=status_table.stock_id' .
            $this->getConnection()->quoteInto(
                ' AND status_table.website_id=?',
                $websiteId
            ),
            array('stock_status')
        );

        return $this;
    }

    /**
     * Add Managed Stock products filter to collection
     *
     * @param bool $isStockManagedInConfig
     * @return $this
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
     * @return $this
     * @throws \Magento\Model\Exception
     */
    public function addQtyFilter($comparisonMethod, $qty)
    {
        $methods = array('<' => 'lt', '>' => 'gt', '=' => 'eq', '<=' => 'lteq', '>=' => 'gteq', '<>' => 'neq');
        if (!isset($methods[$comparisonMethod])) {
            throw new \Magento\Model\Exception(__('%1 is not a correct comparison method.', $comparisonMethod));
        }

        return $this->addFieldToFilter('main_table.qty', array($methods[$comparisonMethod] => $qty));
    }

    /**
     * Initialize select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        return parent::_initSelect()->getSelect()->join(
            array('cp_table' => $this->getTable('catalog_product_entity')),
            'main_table.product_id = cp_table.entity_id',
            array('type_id')
        );
    }
}
