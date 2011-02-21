<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist model
 *
 * @method Mage_Wishlist_Model_Resource_Wishlist _getResource()
 * @method Mage_Wishlist_Model_Resource_Wishlist getResource()
 * @method int getShared()
 * @method Mage_Wishlist_Model_Wishlist setShared(int $value)
 * @method string getSharingCode()
 * @method Mage_Wishlist_Model_Wishlist setSharingCode(string $value)
 * @method string getUpdatedAt()
 * @method Mage_Wishlist_Model_Wishlist setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Wishlist extends Mage_Core_Model_Abstract
{
    /**
     * Wishlist item collection
     *
     * @var Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    protected $_itemCollection = null;

    /**
     * Store filter for wishlist
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store = null;

    /**
     * Shared store ids (website stores)
     *
     * @var array
     */
    protected $_storeIds = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('wishlist/wishlist');
    }

    /**
     * Load wishlist by customer
     *
     * @param mixed $customer
     * @param bool $create Create wishlist if don't exists
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function loadByCustomer($customer, $create = false)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        }

        $customerIdFieldName = $this->_getResource()->getCustomerIdFieldName();
        $this->_getResource()->load($this, $customer, $customerIdFieldName);
        if (!$this->getId() && $create) {
            $this->setCustomerId($customer);
            $this->setSharingCode($this->_getSharingRandomCode());
            $this->save();
        }

        return $this;
    }

    /**
     * Load by sharing code
     *
     * @param string $code
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function loadByCode($code)
    {
        $this->_getResource()->load($this, $code, 'sharing_code');
        if(!$this->getShared()) {
            $this->setId(null);
        }

        return $this;
    }

    /**
     * Retrieve sharing code (random string)
     *
     * @return string
     */
    protected function _getSharingRandomCode()
    {
        return Mage::helper('core')->uniqHash();
    }

    /**
     * Set date of last update for wishlist
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }

    /**
     * Retrieve wishlist item collection
     *
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function getItemCollection()
    {
        if (is_null($this->_itemCollection)) {
            $this->_itemCollection =  Mage::getResourceModel('wishlist/item_collection')
                ->addWishlistFilter($this);
        }

        return $this->_itemCollection;
    }

    /**
     * Retrieve wishlist item collection
     *
     * @param int $itemId
     * @return Mage_Wishlist_Model_Item
     */
    public function getItem($itemId)
    {
        if (!$itemId) {
            return false;
        }
        $item = Mage::getModel('wishlist/item')->load($itemId);
        if ($item->getWishlistId() != $this->getId()) {
            return false;
        }
        return $item;
    }

    /**
     * Retrieve Product collection
     *
     * @deprecated since 1.4.2.0
     * @see Mage_Wishlist_Model_Wishlist::getItemCollection()
     *
     * @return Mage_Wishlist_Model_Mysql4_Item_Collection
     */
    public function getProductCollection()
    {
        throw new Exception("Usage of product collection is deprecated in wishlist.");

        $collection = $this->getData('product_collection');
        if (is_null($collection)) {
            $collection = $this->getItemCollection()
                ->addStoreFilter($this->getStore()->getId())
                ->addWishlistFilter($this)
                ->addWishListSortOrder();

            foreach ($collection as $item) {
                $item->setName($item->getProduct()->getName());
                $item->setSku($item->getProduct()->getSku());
                $item->setPrice($item->getProduct()->getPrice());
                $item->setFinalPrice($item->getProduct()->getFinalPrice());
                $item->setWishlistItemDescription($item->getDescription());
                $item->setProductUrl($item->getProduct()->getProductUrl());
                $item->setDescription($item->getProduct()->getDescription());
            }

            $this->setData('product_collection', $collection);
        }

        return $collection;
    }

    /**
     * Add new item to wishlist
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @param mixed $buyRequest
     * @return Mage_Wishlist_Model_Item
     */
    public function addNewItem($product, $buyRequest = null)
    {
        /* @var $_product Mage_Catalog_Model_Product */
        if ($product instanceof Mage_Catalog_Model_Product) {
            $_product = $product;
        } else {
            $_product = Mage::getModel('catalog/product')->load((int)$product);
        }

        if ($buyRequest instanceof Varien_Object) {
            $_buyRequest = $buyRequest;
        } elseif (is_string($buyRequest)) {
            $_buyRequest = new Varien_Object(unserialize($buyRequest));
        } elseif (is_array($buyRequest)) {
            $_buyRequest = new Varien_Object($buyRequest);
        } else {
            $_buyRequest = new Varien_Object();
        }
        $qty = $_buyRequest->getQty()?$_buyRequest->getQty():$_product->getStockItem()->getMinSaleQty();

        $item = null;
        foreach ($this->getItemCollection() as $_item) {
            if ($_item->isRepresent($_product, $_buyRequest)) {
                $item = $_item;
                break;
            }
        }

        if ($item === null) {
            $item = Mage::getModel('wishlist/item');
            $item->setProductId($_product->getId())
                ->setWishlistId($this->getId())
                ->setAddedAt(now())
                ->setStoreId($this->getStore()->getId())
                ->setBuyRequest($_buyRequest)
                ->setQty($qty)
                ->save();
        } else {
            $item->setBuyRequest($buyRequest)
                ->setQty($item->getQty() + $qty)
                ->save();
        }

        return $item;
    }

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function setCustomerId($customerId)
    {
        return $this->setData($this->_getResource()->getCustomerIdFieldName(), $customerId);
    }

    /**
     * Retrieve customer id
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getCustomerId()
    {
        return $this->getData($this->_getResource()->getCustomerIdFieldName());
    }

    /**
     * Retrieve data for save
     *
     * @return array
     */
    public function getDataForSave()
    {
        $data = array();
        $data[$this->_getResource()->getCustomerIdFieldName()] = $this->getCustomerId();
        $data['shared']      = (int) $this->getShared();
        $data['sharing_code']= $this->getSharingCode();
        return $data;
    }

    /**
     * Retrieve shared store ids for current website or all stores if $current is false
     *
     * @param bool $current Use current website or not
     * @return array
     */
    public function getSharedStoreIds($current = true)
    {
        if (is_null($this->_storeIds)) {
            if ($current) {
                $this->_storeIds = $this->getStore()->getWebsite()->getStoreIds();
            } else {
                $_storeIds = array();
                foreach (Mage::app()->getStores() as $store) {
                    $_storeIds[] = $store->getId();
                }
                $this->_storeIds = $_storeIds;
            }
        }
        return $this->_storeIds;
    }

    /**
     * Set shared store ids
     *
     * @param array $storeIds
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function setSharedStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    /**
     * Retrieve wishlist store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->setStore(Mage::app()->getStore());
        }
        return $this->_store;
    }

    /**
     * Set wishlist store
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve wishlist items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->_getResource()->fetchItemsCount($this);
    }

    /**
     * Retrieve wishlist has salable item(s)
     *
     * @return bool
     */
    public function isSalable()
    {
        foreach ($this->getItemCollection() as $item) {
            if ($item->getProduct()->getIsSalable()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check customer is owner this wishlist
     *
     * @param int $customerId
     * @return bool
     */
    public function isOwner($customerId)
    {
        return $customerId == $this->getCustomerId();
    }


    /**
     * Update wishlist Item and set data from request
     *
     * @param int $itemId
     * @param Varien_Object $buyRequest
     */
    public function updateItem($itemId, $buyRequest) {
        $item = $this->getItem((int)$itemId);
        if (!$item) {
            Mage::throwException(Mage::helper('wishlist')->__('Cannot specify wishlist item.'));
        }

        $product = $item->getProduct();
        $newItem = null;

        foreach ($this->getItemCollection() as $_item) {
            if ($item->getId() != $_item->getId() && $_item->isRepresent($product, $buyRequest)) {
                $newItem = $_item;
                break;
            }
        }

        if ($newItem === null) {
            $item->setBuyRequest($buyRequest)
                ->setQty($buyRequest->getQty())
                ->save();
        } else {
            $item->delete();
            $newItem->setBuyRequest($buyRequest)
                ->setQty($newItem->getQty() + $buyRequest->getQty())
                ->save();
        }
    }
}
