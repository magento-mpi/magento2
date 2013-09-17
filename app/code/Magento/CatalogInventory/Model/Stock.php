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
 * Stock model
 *
 * @method Magento_CatalogInventory_Model_Resource_Stock _getResource()
 * @method Magento_CatalogInventory_Model_Resource_Stock getResource()
 * @method string getStockName()
 * @method Magento_CatalogInventory_Model_Stock setStockName(string $value)
 */
class Magento_CatalogInventory_Model_Stock extends Magento_Core_Model_Abstract
{
    const BACKORDERS_NO             = 0;
    const BACKORDERS_YES_NONOTIFY   = 1;
    const BACKORDERS_YES_NOTIFY     = 2;

    const STOCK_OUT_OF_STOCK        = 0;
    const STOCK_IN_STOCK            = 1;

    const DEFAULT_STOCK_ID          = 1;

    /**
     * Catalog inventory data
     *
     * @var Magento_CatalogInventory_Helper_Data
     */
    protected $_catalogInventoryData;

    /**
     * Store model manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Stock item factory
     *
     * @var Magento_CatalogInventory_Model_Stock_ItemFactory
     */
    protected $_stockItemFactory;

    /**
     * Construct
     * 
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_CatalogInventory_Model_Stock_ItemFactory $stockItemFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CatalogInventory_Model_Stock_ItemFactory $stockItemFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        
        $this->_catalogInventoryData = $catalogInventoryData;
        $this->_storeManager = $storeManager;
        $this->_stockItemFactory = $stockItemFactory;
    }

    protected function _construct()
    {
        $this->_init('Magento_CatalogInventory_Model_Resource_Stock');
    }

    /**
     * Retrieve stock identifier
     *
     * @return int
     */
    public function getId()
    {
        return self::DEFAULT_STOCK_ID;
    }

    /**
     * Add stock item objects to products
     *
     * @param   collection $products
     * @return  Magento_CatalogInventory_Model_Stock
     */
    public function addItemsToProducts($productCollection)
    {
        $items = $this->getItemCollection()
            ->addProductsFilter($productCollection)
            ->joinStockStatus($productCollection->getStoreId())
            ->load();
        $stockItems = array();
        foreach ($items as $item) {
            $stockItems[$item->getProductId()] = $item;
        }
        foreach ($productCollection as $product) {
            if (isset($stockItems[$product->getId()])) {
                $stockItems[$product->getId()]->assignProduct($product);
            }
        }
        return $this;
    }

    /**
     * Retrieve items collection object with stock filter
     *
     * @return Magento_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    public function getItemCollection()
    {
        return Mage::getResourceModel('Magento_CatalogInventory_Model_Resource_Stock_Item_Collection')
            ->addStockFilter($this->getId());
    }

    /**
     * Prepare array($productId=>$qty) based on array($productId => array('qty'=>$qty, 'item'=>$stockItem))
     *
     * @param array $items
     */
    protected function _prepareProductQtys($items)
    {
        $qtys = array();
        foreach ($items as $productId => $item) {
            if (empty($item['item'])) {
                $stockItem = $this->_stockItemFactory->create()->loadByProduct($productId);
            } else {
                $stockItem = $item['item'];
            }
            $canSubtractQty = $stockItem->getId() && $stockItem->canSubtractQty();
            if ($canSubtractQty && $this->_catalogInventoryData->isQty($stockItem->getTypeId())) {
                $qtys[$productId] = $item['qty'];
            }
        }
        return $qtys;
    }

    /**
     * Subtract product qtys from stock.
     * Return array of items that require full save
     *
     * @param array $items
     * @return array
     * @throws Magento_Core_Exception
     */
    public function registerProductsSale($items)
    {
        $qtys = $this->_prepareProductQtys($items);
        /** @var \Magento_CatalogInventory_Model_Stock_Item $item */
        $item = $this->_stockItemFactory->create();
        $this->_getResource()->beginTransaction();
        $stockInfo = $this->_getResource()->getProductsStock($this, array_keys($qtys), true);
        $fullSaveItems = array();
        foreach ($stockInfo as $itemInfo) {
            $item->setData($itemInfo);
            if (!$item->checkQty($qtys[$item->getProductId()])) {
                $this->_getResource()->commit();
                throw new Magento_Core_Exception(
                    __('Not all of your products are available in the requested quantity.'));
            }
            $item->subtractQty($qtys[$item->getProductId()]);
            if (!$item->verifyStock() || $item->verifyNotification()) {
                $fullSaveItems[] = clone $item;
            }
        }
        $this->_getResource()->correctItemsQty($this, $qtys, '-');
        $this->_getResource()->commit();
        return $fullSaveItems;
    }

    /**
     *
     * @param unknown_type $items
     */
    public function revertProductsSale($items)
    {
        $qtys = $this->_prepareProductQtys($items);
        $this->_getResource()->correctItemsQty($this, $qtys, '+');
        return $this;
    }

    /**
     * Subtract ordered qty for product
     *
     * @param  Magento_Object $item
     * @return Magento_CatalogInventory_Model_Stock
     * @throws Magento_Core_Exception
     */
    public function registerItemSale(Magento_Object $item)
    {
        $productId = $item->getProductId();
        if ($productId) {
            /** @var \Magento_CatalogInventory_Model_Stock_Item $stockItem */
            $stockItem = $this->_stockItemFactory->create()->loadByProduct($productId);
            if ($this->_catalogInventoryData->isQty($stockItem->getTypeId())) {
                if ($item->getStoreId()) {
                    $stockItem->setStoreId($item->getStoreId());
                }
                if ($stockItem->checkQty($item->getQtyOrdered()) || $this->_storeManager->getStore()->isAdmin()) {
                    $stockItem->subtractQty($item->getQtyOrdered());
                    $stockItem->save();
                }
            }
        }
        else {
            throw new Magento_Core_Exception(__('We cannot specify a product identifier for the order item.'));
        }
        return $this;
    }

    /**
     * Get back to stock (when order is canceled or whatever else)
     *
     * @param int $productId
     * @param numeric $qty
     * @return Magento_CatalogInventory_Model_Stock
     */
    public function backItemQty($productId, $qty)
    {
        /** @var \Magento_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = $this->_stockItemFactory->create()->loadByProduct($productId);
        if ($stockItem->getId() && $this->_catalogInventoryData->isQty($stockItem->getTypeId())) {
            $stockItem->addQty($qty);
            if ($stockItem->getCanBackInStock() && $stockItem->getQty() > $stockItem->getMinQty()) {
                $stockItem->setIsInStock(true)
                    ->setStockStatusChangedAutomaticallyFlag(true);
            }
            $stockItem->save();
        }
        return $this;
    }

    /**
     * Lock stock items for product ids array
     *
     * @param   array $productIds
     * @return  Magento_CatalogInventory_Model_Stock
     */
    public function lockProductItems($productIds)
    {
        $this->_getResource()->lockProductItems($this, $productIds);
        return $this;
    }

    /**
     * Adds filtering for collection to return only in stock products
     *
     * @param Magento_Catalog_Model_Resource_Product_Link_Product_Collection $collection
     * @return Magento_CatalogInventory_Model_Stock $this
     */
    public function addInStockFilterToCollection($collection)
    {
        $this->getResource()->setInStockFilterToCollection($collection);
        return $this;
    }
}
