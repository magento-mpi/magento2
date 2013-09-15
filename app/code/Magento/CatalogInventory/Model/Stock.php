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
 * @method \Magento\CatalogInventory\Model\Resource\Stock _getResource()
 * @method \Magento\CatalogInventory\Model\Resource\Stock getResource()
 * @method string getStockName()
 * @method \Magento\CatalogInventory\Model\Stock setStockName(string $value)
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model;

class Stock extends \Magento\Core\Model\AbstractModel
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
     * @var \Magento\CatalogInventory\Helper\Data
     */
    protected $_catalogInventoryData = null;

    /**
     * @param \Magento\CatalogInventory\Helper\Data $catalogInventoryData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\CatalogInventory\Helper\Data $catalogInventoryData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogInventoryData = $catalogInventoryData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento\CatalogInventory\Model\Resource\Stock');
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
     * @return  \Magento\CatalogInventory\Model\Stock
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
     * @return unknown
     */
    public function getItemCollection()
    {
        return \Mage::getResourceModel('Magento\CatalogInventory\Model\Resource\Stock\Item\Collection')
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
                $stockItem = \Mage::getModel('Magento\CatalogInventory\Model\Stock\Item')->loadByProduct($productId);
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
     */
    public function registerProductsSale($items)
    {
        $qtys = $this->_prepareProductQtys($items);
        $item = \Mage::getModel('Magento\CatalogInventory\Model\Stock\Item');
        $this->_getResource()->beginTransaction();
        $stockInfo = $this->_getResource()->getProductsStock($this, array_keys($qtys), true);
        $fullSaveItems = array();
        foreach ($stockInfo as $itemInfo) {
            $item->setData($itemInfo);
            if (!$item->checkQty($qtys[$item->getProductId()])) {
                $this->_getResource()->commit();
                \Mage::throwException(__('Not all of your products are available in the requested quantity.'));
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
     * @param   \Magento\Object $item
     * @return  \Magento\CatalogInventory\Model\Stock
     */
    public function registerItemSale(\Magento\Object $item)
    {
        $productId = $item->getProductId();
        if ($productId) {
            $stockItem = \Mage::getModel('Magento\CatalogInventory\Model\Stock\Item')->loadByProduct($productId);
            if ($this->_catalogInventoryData->isQty($stockItem->getTypeId())) {
                if ($item->getStoreId()) {
                    $stockItem->setStoreId($item->getStoreId());
                }
                if ($stockItem->checkQty($item->getQtyOrdered()) || \Mage::app()->getStore()->isAdmin()) {
                    $stockItem->subtractQty($item->getQtyOrdered());
                    $stockItem->save();
                }
            }
        }
        else {
            \Mage::throwException(__('We cannot specify a product identifier for the order item.'));
        }
        return $this;
    }

    /**
     * Get back to stock (when order is canceled or whatever else)
     *
     * @param int $productId
     * @param numeric $qty
     * @return \Magento\CatalogInventory\Model\Stock
     */
    public function backItemQty($productId, $qty)
    {
        $stockItem = \Mage::getModel('Magento\CatalogInventory\Model\Stock\Item')->loadByProduct($productId);
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
     * @return  \Magento\CatalogInventory\Model\Stock
     */
    public function lockProductItems($productIds)
    {
        $this->_getResource()->lockProductItems($this, $productIds);
        return $this;
    }

    /**
     * Adds filtering for collection to return only in stock products
     *
     * @param \Magento\Catalog\Model\Resource\Product\Link\Product\Collection $collection
     * @return \Magento\CatalogInventory\Model\Stock $this
     */
    public function addInStockFilterToCollection($collection)
    {
        $this->getResource()->setInStockFilterToCollection($collection);
        return $this;
    }
}
