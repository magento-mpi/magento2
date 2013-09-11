<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist item collection
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Model\Resource\Item;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Product Visibility Filter to product collection flag
     *
     * @var bool
     */
    protected $_productVisible = false;

    /**
     * Product Salable Filter to product collection flag
     *
     * @var bool
     */
    protected $_productSalable = false;

    /**
     * If product out of stock, its item will be removed after load
     *
     * @var bool
     */
    protected $_productInStock = false;

    /**
     * Product Ids array
     *
     * @var array
     */
    protected $_productIds = array();

    /**
     * Store Ids array
     *
     * @var array
     */
    protected $_storeIds = array();

    /**
     * Add days in whishlist filter of product collection
     *
     * @var boolean
     */
    protected $_addDaysInWishlist = false;

    /**
     * Sum of items collection qty
     *
     * @var int
     */
    protected $_itemsQty;

    /**
     * Whether product name attribute value table is joined in select
     *
     * @var boolean
     */
    protected $_isProductNameJoined = false;

    /**
     * Initialize resource model for collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magento\Wishlist\Model\Item', 'Magento\Wishlist\Model\Resource\Item');
        $this->addFilterToMap('store_id', 'main_table.store_id');
    }

    /**
     * After load processing
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        /**
         * Assign products
         */
        $this->_assignOptions();
        $this->_assignProducts();
        $this->resetItemsDataChanged();

        $this->getPageSize();

        return $this;
    }

    /**
     * Add options to items
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    protected function _assignOptions()
    {
        $itemIds = array_keys($this->_items);
        /* @var $optionCollection \Magento\Wishlist\Model\Resource\Item\Option\Collection */
        $optionCollection = \Mage::getModel('Magento\Wishlist\Model\Item\Option')->getCollection();
        $optionCollection->addItemFilter($itemIds);

        /* @var $item \Magento\Wishlist\Model\Item */
        foreach ($this as $item) {
            $item->setOptions($optionCollection->getOptionsByItem($item));
        }
        $productIds = $optionCollection->getProductIds();
        $this->_productIds = array_merge($this->_productIds, $productIds);

        return $this;
    }

    /**
     * Add products to items and item options
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    protected function _assignProducts()
    {
        \Magento\Profiler::start('WISHLIST:'.__METHOD__, array('group' => 'WISHLIST', 'method' => __METHOD__));
        $productIds = array();

        $isStoreAdmin = \Mage::app()->getStore()->isAdmin();

        $storeIds = array();
        foreach ($this as $item) {
            $productIds[$item->getProductId()] = 1;
            if ($isStoreAdmin && !in_array($item->getStoreId(), $storeIds)) {
                $storeIds[] = $item->getStoreId();
            }
        }
        if (!$isStoreAdmin) {
            $storeIds = $this->_storeIds;
        }

        $this->_productIds = array_merge($this->_productIds, array_keys($productIds));
        $attributes = \Mage::getSingleton('Magento\Wishlist\Model\Config')->getProductAttributes();
        $productCollection = \Mage::getModel('Magento\Catalog\Model\Product')->getCollection();
        foreach ($storeIds as $id) {
            $productCollection->addStoreFilter($id);
        }

        if ($this->_productVisible) {
            $productCollection->setVisibility(\Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInSiteIds());
        }

        $productCollection->addPriceData()
            ->addTaxPercents()
            ->addIdFilter($this->_productIds)
            ->addAttributeToSelect($attributes)
            ->addOptionsToResult()
            ->addUrlRewrite();

        if ($this->_productSalable) {
            $productCollection = \Mage::helper('Magento\Adminhtml\Helper\Sales')->applySalableProductTypesFilter($productCollection);
        }

        \Mage::dispatchEvent('wishlist_item_collection_products_after_load', array(
            'product_collection' => $productCollection
        ));

        $checkInStock = $this->_productInStock && !\Mage::helper('Magento\CatalogInventory\Helper\Data')->isShowOutOfStock();

        foreach ($this as $item) {
            $product = $productCollection->getItemById($item->getProductId());
            if ($product) {
                if ($checkInStock && !$product->isInStock()) {
                    $this->removeItemByKey($item->getId());
                } else {
                    $product->setCustomOptions(array());
                    $item->setProduct($product);
                    $item->setProductName($product->getName());
                    $item->setName($product->getName());
                    $item->setPrice($product->getPrice());
                }
            } else {
                $item->isDeleted(true);
            }
        }

        \Magento\Profiler::stop('WISHLIST:'.__METHOD__);

        return $this;
    }

    /**
     * Add filter by wishlist object
     *
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function addWishlistFilter(\Magento\Wishlist\Model\Wishlist $wishlist)
    {
        $this->addFieldToFilter('wishlist_id', $wishlist->getId());
        return $this;
    }

    /**
     * Add filtration by customer id
     *
     * @param int $customerId
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function addCustomerIdFilter($customerId)
    {
        $this->getSelect()
            ->join(
            array('wishlist' => $this->getTable('wishlist')),
            'main_table.wishlist_id = wishlist.wishlist_id',
            array()
        )
            ->where('wishlist.customer_id = ?', $customerId);
        return $this;
    }

    /**
     * Add filter by shared stores
     *
     * @param array $storeIds
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function addStoreFilter($storeIds = array())
    {
        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }
        $this->_storeIds = $storeIds;
        $this->addFieldToFilter('store_id', array('in' => $this->_storeIds));

        return $this;
    }

    /**
     * Add items store data to collection
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function addStoreData()
    {
        $storeTable = \Mage::getSingleton('Magento\Core\Model\Resource')->getTableName('core_store');
        $this->getSelect()->join(array('store'=>$storeTable), 'main_table.store_id=store.store_id', array(
            'store_name'=>'name',
            'item_store_id' => 'store_id'
        ));
        return $this;
    }

    /**
     * Reset sort order
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function resetSortOrder()
    {
        $this->getSelect()->reset(\Zend_Db_Select::ORDER);
        return $this;
    }

    /**
     * Set product Visibility Filter to product collection flag
     *
     * @param bool $flag
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function setVisibilityFilter($flag = true)
    {
        $this->_productVisible = (bool)$flag;
        return $this;
    }

    /**
     * Set Salable Filter.
     * This filter apply Salable Product Types Filter to product collection.
     *
     * @param bool $flag
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function setSalableFilter($flag = true)
    {
        $this->_productSalable = (bool)$flag;
        return $this;
    }

    /**
     * Set In Stock Filter.
     * This filter remove items with no salable product.
     *
     * @param bool $flag
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function setInStockFilter($flag = true)
    {
        $this->_productInStock = (bool)$flag;
        return $this;
    }

    /**
     * Set flag of adding days in wishlist
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function addDaysInWishlist()
    {
        $this->_addDaysInWishlist = true;
        return $this;
    }

    /**
     * Adds filter on days in wishlist
     *
     * $constraints may contain 'from' and 'to' indexes with number of days to look for items
     *
     * @param array $constraints
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function addDaysFilter($constraints)
    {
        if (!is_array($constraints)) {
            return $this;
        }

        $filter = array();

        $now = \Mage::getSingleton('Magento\Core\Model\Date')->date();
        $gmtOffset = (int) \Mage::getSingleton('Magento\Core\Model\Date')->getGmtOffset();
        if (isset($constraints['from'])) {
            $lastDay = new \Zend_Date($now, \Magento\Date::DATETIME_INTERNAL_FORMAT);
            $lastDay->subSecond($gmtOffset)
                ->subDay(intval($constraints['from']));
            $filter['to'] = $lastDay;
        }

        if (isset($constraints['to'])) {
            $firstDay = new \Zend_Date($now, \Magento\Date::DATETIME_INTERNAL_FORMAT);
            $firstDay->subSecond($gmtOffset)
                ->subDay(intval($constraints['to']) + 1);
            $filter['from'] = $firstDay;
        }

        if ($filter) {
            $filter['datetime'] = true;
            $this->addFieldToFilter('added_at', $filter);
        }

        return $this;
    }

    /**
     * Joins product name attribute value to use it in WHERE and ORDER clauses
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    protected function _joinProductNameTable()
    {
        if (!$this->_isProductNameJoined) {
            $entityTypeId = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Config')
                ->getEntityTypeId();
            $attribute = \Mage::getModel('Magento\Catalog\Model\Entity\Attribute')
                ->loadByCode($entityTypeId, 'name');

            $storeId = \Mage::app()->getStore()->getId();

            $this->getSelect()
                ->join(
                array('product_name_table' => $attribute->getBackendTable()),
                'product_name_table.entity_id=main_table.product_id' .
                    ' AND product_name_table.store_id=' . $storeId .
                    ' AND product_name_table.attribute_id=' . $attribute->getId().
                    ' AND product_name_table.entity_type_id=' . $entityTypeId,
                array()
            );

            $this->_isProductNameJoined = true;
        }
        return $this;
    }

    /**
     * Adds filter on product name
     *
     * @param string $productName
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function addProductNameFilter($productName)
    {
        $this->_joinProductNameTable();
        $this->getSelect()
            ->where('INSTR(product_name_table.value, ?)', $productName);

        return $this;
    }

    /**
     * Sets ordering by product name
     *
     * @param string $dir
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function setOrderByProductName($dir)
    {
        $this->_joinProductNameTable();
        $this->getSelect()->order('product_name_table.value ' . $dir);
        return $this;
    }

    /**
     * Get sum of items collection qty
     *
     * @return int
     */
    public function getItemsQty()
    {
        if (is_null($this->_itemsQty)) {
            $this->_itemsQty = 0;
            foreach ($this as $wishlistItem) {
                $qty = $wishlistItem->getQty();
                $this->_itemsQty += ($qty === 0) ? 1 : $qty;
            }
        }

        return (int)$this->_itemsQty;
    }

    /**
     * @return \Magento\Wishlist\Model\Resource\Item\Collection|\Magento\Data\Collection\Db
     */
    protected function _afterLoadData()
    {
        parent::_afterLoadData();

        if ($this->_addDaysInWishlist) {
            $gmtOffset = (int) \Mage::getSingleton('Magento\Core\Model\Date')->getGmtOffset();
            $nowTimestamp = \Mage::getSingleton('Magento\Core\Model\Date')->timestamp();

            foreach ($this as $wishlistItem) {
                $wishlistItemTimestamp = \Mage::getSingleton('Magento\Core\Model\Date')
                    ->timestamp($wishlistItem->getAddedAt());

                $wishlistItem->setDaysInWishlist(
                    (int) (($nowTimestamp - $gmtOffset - $wishlistItemTimestamp) / 24 / 60 / 60)
                );
            }
        }

        return $this;
    }
}
