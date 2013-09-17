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
 * Wishlist model
 *
 * @method Magento_Wishlist_Model_Resource_Wishlist _getResource()
 * @method Magento_Wishlist_Model_Resource_Wishlist getResource()
 * @method int getShared()
 * @method Magento_Wishlist_Model_Wishlist setShared(int $value)
 * @method string getSharingCode()
 * @method Magento_Wishlist_Model_Wishlist setSharingCode(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Wishlist_Model_Wishlist setUpdatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Model_Wishlist extends Magento_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'wishlist';
    /**
     * Wishlist item collection
     *
     * @var Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected $_itemCollection = null;

    /**
     * Store filter for wishlist
     *
     * @var Magento_Core_Model_Store
     */
    protected $_store = null;

    /**
     * Shared store ids (website stores)
     *
     * @var array
     */
    protected $_storeIds = null;

    /**
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Catalog product
     *
     * @var Magento_Catalog_Helper_Product
     */
    protected $_catalogProduct = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Catalog_Helper_Product $catalogProduct
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Wishlist_Model_Resource_Wishlist $resource
     * @param Magento_Wishlist_Model_Resource_Wishlist_Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Catalog_Helper_Product $catalogProduct,
        Magento_Core_Helper_Data $coreData,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Wishlist_Model_Resource_Wishlist $resource,
        Magento_Wishlist_Model_Resource_Wishlist_Collection $resourceCollection,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_catalogProduct = $catalogProduct;
        $this->_coreData = $coreData;
        $this->_wishlistData = $wishlistData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Load wishlist by customer
     *
     * @param mixed $customer
     * @param bool $create Create wishlist if don't exists
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function loadByCustomer($customer, $create = false)
    {
        if ($customer instanceof Magento_Customer_Model_Customer) {
            $customer = $customer->getId();
        }

        $customer = (int) $customer;
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
     * Retrieve wishlist name
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->_getData('name');
        if (!strlen($name)) {
            return $this->_wishlistData->getDefaultWishlistName();
        }
        return $name;
    }

    /**
     * Set random sharing code
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function generateSharingCode()
    {
        $this->setSharingCode($this->_getSharingRandomCode());
        return $this;
    }

    /**
     * Load by sharing code
     *
     * @param string $code
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function loadByCode($code)
    {
        $this->_getResource()->load($this, $code, 'sharing_code');
        if (!$this->getShared()) {
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
        return $this->_coreData->uniqHash();
    }

    /**
     * Set date of last update for wishlist
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->setUpdatedAt(Mage::getSingleton('Magento_Core_Model_Date')->gmtDate());
        return $this;
    }

    /**
     * Save related items
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if (null !== $this->_itemCollection) {
            $this->getItemCollection()->save();
        }
        return $this;
    }

    /**
     * Add catalog product object data to wishlist
     *
     * @param   Magento_Catalog_Model_Product $product
     * @param   int $qty
     * @param   bool $forciblySetQty
     *
     * @return  Magento_Wishlist_Model_Item
     */
    protected function _addCatalogProduct(Magento_Catalog_Model_Product $product, $qty = 1, $forciblySetQty = false)
    {
        $item = null;
        foreach ($this->getItemCollection() as $_item) {
            if ($_item->representProduct($product)) {
                $item = $_item;
                break;
            }
        }

        if ($item === null) {
            $storeId = $product->hasWishlistStoreId() ? $product->getWishlistStoreId() : $this->getStore()->getId();
            $item = Mage::getModel('Magento_Wishlist_Model_Item');
            $item->setProductId($product->getId())
                ->setWishlistId($this->getId())
                ->setAddedAt(now())
                ->setStoreId($storeId)
                ->setOptions($product->getCustomOptions())
                ->setProduct($product)
                ->setQty($qty)
                ->save();
            if ($item->getId()) {
                $this->getItemCollection()->addItem($item);
            }
        } else {
            $qty = $forciblySetQty ? $qty : $item->getQty() + $qty;
            $item->setQty($qty)
                ->save();
        }

        $this->addItem($item);

        return $item;
    }

    /**
     * Retrieve wishlist item collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    public function getItemCollection()
    {
        if (is_null($this->_itemCollection)) {
            /** @var $currentWebsiteOnly boolean */
            $currentWebsiteOnly = !Mage::app()->getStore()->isAdmin();
            $this->_itemCollection =  Mage::getResourceModel('Magento_Wishlist_Model_Resource_Item_Collection')
                ->addWishlistFilter($this)
                ->addStoreFilter($this->getSharedStoreIds($currentWebsiteOnly))
                ->setVisibilityFilter();
        }

        return $this->_itemCollection;
    }

    /**
     * Retrieve wishlist item collection
     *
     * @param int $itemId
     * @return Magento_Wishlist_Model_Item
     */
    public function getItem($itemId)
    {
        if (!$itemId) {
            return false;
        }
        return $this->getItemCollection()->getItemById($itemId);
    }

    /**
     * Adding item to wishlist
     *
     * @param   Magento_Wishlist_Model_Item $item
     * @return  Magento_Wishlist_Model_Wishlist
     */
    public function addItem(Magento_Wishlist_Model_Item $item)
    {
        $item->setWishlist($this);
        if (!$item->getId()) {
            $this->getItemCollection()->addItem($item);
            $this->_eventManager->dispatch('wishlist_add_item', array('item' => $item));
        }
        return $this;
    }

    /**
     * Adds new product to wishlist.
     * Returns new item or string on error.
     *
     * @param int|Magento_Catalog_Model_Product $product
     * @param mixed $buyRequest
     * @param bool $forciblySetQty
     * @return Magento_Wishlist_Model_Item|string
     */
    public function addNewItem($product, $buyRequest = null, $forciblySetQty = false)
    {
        /*
         * Always load product, to ensure:
         * a) we have new instance and do not interfere with other products in wishlist
         * b) product has full set of attributes
         */
        if ($product instanceof Magento_Catalog_Model_Product) {
            $productId = $product->getId();
            // Maybe force some store by wishlist internal properties
            $storeId = $product->hasWishlistStoreId() ? $product->getWishlistStoreId() : $product->getStoreId();
        } else {
            $productId = (int) $product;
            if ($buyRequest->getStoreId()) {
                $storeId = $buyRequest->getStoreId();
            } else {
                $storeId = Mage::app()->getStore()->getId();
            }
        }

        /* @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product')
            ->setStoreId($storeId)
            ->load($productId);

        if ($buyRequest instanceof Magento_Object) {
            $_buyRequest = $buyRequest;
        } elseif (is_string($buyRequest)) {
            $_buyRequest = new Magento_Object(unserialize($buyRequest));
        } elseif (is_array($buyRequest)) {
            $_buyRequest = new Magento_Object($buyRequest);
        } else {
            $_buyRequest = new Magento_Object();
        }

        $cartCandidates = $product->getTypeInstance()
            ->processConfiguration($_buyRequest, $product);

        /**
         * Error message
         */
        if (is_string($cartCandidates)) {
            return $cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = array($cartCandidates);
        }

        $errors = array();
        $items = array();

        foreach ($cartCandidates as $candidate) {
            if ($candidate->getParentProductId()) {
                continue;
            }
            $candidate->setWishlistStoreId($storeId);

            $qty = $candidate->getQty() ? $candidate->getQty() : 1; // No null values as qty. Convert zero to 1.
            $item = $this->_addCatalogProduct($candidate, $qty, $forciblySetQty);
            $items[] = $item;

            // Collect errors instead of throwing first one
            if ($item->getHasError()) {
                $errors[] = $item->getMessage();
            }
        }

        $this->_eventManager->dispatch('wishlist_product_add_after', array('items' => $items));

        return $item;
    }

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function setCustomerId($customerId)
    {
        return $this->setData($this->_getResource()->getCustomerIdFieldName(), $customerId);
    }

    /**
     * Retrieve customer id
     *
     * @return int
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
        if (is_null($this->_storeIds) || !is_array($this->_storeIds)) {
            if ($current) {
                $this->_storeIds = $this->getStore()->getWebsite()->getStoreIds();
            } else {
                $_storeIds = array();
                $stores = Mage::app()->getStores();
                foreach ($stores as $store) {
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
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function setSharedStoreIds($storeIds)
    {
        $this->_storeIds = (array) $storeIds;
        return $this;
    }

    /**
     * Retrieve wishlist store object
     *
     * @return Magento_Core_Model_Store
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
     * @param Magento_Core_Model_Store $store
     * @return Magento_Wishlist_Model_Wishlist
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
        return $this->getItemCollection()->getSize();
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
     * $params sets how current item configuration must be taken into account and additional options.
     * It's passed to Magento_Catalog_Helper_Product->addParamsToBuyRequest() to compose resulting buyRequest.
     *
     * Basically it can hold
     * - 'current_config', Magento_Object or array - current buyRequest that configures product in this item,
     *   used to restore currently attached files
     * - 'files_prefix': string[a-z0-9_] - prefix that was added at frontend to names of file options (file inputs),
     * so they won't intersect with other submitted options
     *
     * For more options see Magento_Catalog_Helper_Product->addParamsToBuyRequest()
     *
     * @param int|Magento_Wishlist_Model_Item $itemId
     * @param Magento_Object $buyRequest
     * @param null|array|Magento_Object $params
     * @return Magento_Wishlist_Model_Wishlist
     *
     * @see Magento_Catalog_Helper_Product::addParamsToBuyRequest()
     */
    public function updateItem($itemId, $buyRequest, $params = null)
    {
        $item = null;
        if ($itemId instanceof Magento_Wishlist_Model_Item) {
            $item = $itemId;
        } else {
            $item = $this->getItem((int)$itemId);
        }
        if (!$item) {
            Mage::throwException(__('We can\'t specify a wish list item.'));
        }

        $product = $item->getProduct();
        $productId = $product->getId();
        if ($productId) {
            if (!$params) {
                $params = new Magento_Object();
            } else if (is_array($params)) {
                $params = new Magento_Object($params);
            }
            $params->setCurrentConfig($item->getBuyRequest());
            $buyRequest = $this->_catalogProduct->addParamsToBuyRequest($buyRequest, $params);

            $product->setWishlistStoreId($item->getStoreId());
            $items = $this->getItemCollection();
            $isForceSetQuantity = true;
            foreach ($items as $_item) {
                /* @var $_item Magento_Wishlist_Model_Item */
                if ($_item->getProductId() == $product->getId()
                    && $_item->representProduct($product)
                    && $_item->getId() != $item->getId()) {
                    // We do not add new wishlist item, but updating the existing one
                    $isForceSetQuantity = false;
                }
            }
            $resultItem = $this->addNewItem($product, $buyRequest, $isForceSetQuantity);
            /**
             * Error message
             */
            if (is_string($resultItem)) {
                Mage::throwException(__($resultItem));
            }

            if ($resultItem->getId() != $itemId) {
                if ($resultItem->getDescription() != $item->getDescription()) {
                    $resultItem->setDescription($item->getDescription())->save();
                }
                $item->isDeleted(true);
                $this->setDataChanges(true);
            } else {
                $resultItem->setQty($buyRequest->getQty() * 1);
                $resultItem->setOrigData('qty', 0);
            }
        } else {
            Mage::throwException(__('The product does not exist.'));
        }
        return $this;
    }

    /**
     * Save wishlist.
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function save()
    {
        $this->_hasDataChanges = true;
        return parent::save();
    }
}
