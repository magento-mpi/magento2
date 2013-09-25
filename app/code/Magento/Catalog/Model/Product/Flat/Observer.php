<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Flat observer
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_Product_Flat_Observer
{
    /**
     * Catalog product flat
     *
     * @var Magento_Catalog_Helper_Product_Flat
     */
    protected $_catalogProductFlat = null;

    /**
     * Catalog product flat indexer
     *
     * @var Magento_Catalog_Model_Product_Flat_Indexer
     */
    protected $_catalogProductFlatIndexer;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Product_Flat_Indexer $catalogProductFlatIndexer
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Product_Flat_Indexer $catalogProductFlatIndexer,
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat
    ) {
        $this->_storeManager = $storeManager;
        $this->_catalogProductFlatIndexer = $catalogProductFlatIndexer;
        $this->_catalogProductFlat = $catalogProductFlat;
    }

    /**
     * Retrieve Catalog Product Flat Helper
     *
     * @return Magento_Catalog_Helper_Product_Flat
     */
    protected function _getHelper()
    {
        return $this->_catalogProductFlat;
    }

    /**
     * Retrieve Catalog Product Flat Indexer model
     *
     * @return Magento_Catalog_Model_Product_Flat_Indexer
     */
    protected function _getIndexer() {
        return $this->_catalogProductFlatIndexer;
    }

    /**
     * Catalog Entity attribute after save process
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Product_Flat_Observer
     */
    public function catalogEntityAttributeSaveAfter(Magento_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $attribute = $observer->getEvent()->getAttribute();
        /* @var $attribute Magento_Catalog_Model_Entity_Attribute */

        $enableBefore   = ($attribute->getOrigData('backend_type') == 'static')
            || ($this->_getHelper()->isAddFilterableAttributes() && $attribute->getOrigData('is_filterable') > 0)
            || ($attribute->getOrigData('used_in_product_listing') == 1)
            || ($attribute->getOrigData('used_for_sort_by') == 1);
        $enableAfter    = ($attribute->getData('backend_type') == 'static')
            || ($this->_getHelper()->isAddFilterableAttributes() && $attribute->getData('is_filterable') > 0)
            || ($attribute->getData('used_in_product_listing') == 1)
            || ($attribute->getData('used_for_sort_by') == 1);

        if (!$enableAfter && !$enableBefore) {
            return $this;
        }

        if ($enableBefore && !$enableAfter) {
            // delete attribute data from flat
            $this->_getIndexer()->prepareDataStorage();
        }
        else {
            $this->_getIndexer()->updateAttribute($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * Catalog Product Status Update
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Product_Flat_Observer
     */
    public function catalogProductStatusUpdate(Magento_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $productId  = $observer->getEvent()->getProductId();
        $status     = $observer->getEvent()->getStatus();
        $storeId    = $observer->getEvent()->getStoreId();
        $storeId    = $storeId > 0 ? $storeId : null;

        $this->_getIndexer()->updateProductStatus($productId, $status, $storeId);

        return $this;
    }

    /**
     * Catalog Product Website(s) update
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Product_Flat_Observer
     */
    public function catalogProductWebsiteUpdate(Magento_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $websiteIds = $observer->getEvent()->getWebsiteIds();
        $productIds = $observer->getEvent()->getProductIds();

        foreach ($websiteIds as $websiteId) {
            $website = $this->_storeManager->getWebsite($websiteId);
            foreach ($website->getStores() as $store) {
                if ($observer->getEvent()->getAction() == 'remove') {
                    $this->_getIndexer()->removeProduct($productIds, $store->getId());
                }
                else {
                    $this->_getIndexer()->updateProduct($productIds, $store->getId());
                }
            }
        }

        return $this;
    }

    /**
     * Catalog Product After Save
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Product_Flat_Observer
     */
    public function catalogProductSaveAfter(Magento_Event_Observer $observer) {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $product   = $observer->getEvent()->getProduct();
        $productId = $product->getId();

        $this->_getIndexer()->saveProduct($productId);

        return $this;
    }

    /**
     * Add new store flat process
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Product_Flat_Observer
     */
    public function storeAdd(Magento_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $store = $observer->getEvent()->getStore();
        /* @var $store Magento_Core_Model_Store */
        $this->_getIndexer()->rebuild($store->getId());

        return $this;
    }

    /**
     * Store edit action, check change store group
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Product_Flat_Observer
     */
    public function storeEdit(Magento_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $store = $observer->getEvent()->getStore();
        /* @var $store Magento_Core_Model_Store */
        if ($store->dataHasChangedFor('group_id')) {
            $this->_getIndexer()->rebuild($store->getId());
        }

        return $this;
    }

    /**
     * Store delete after process
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Product_Flat_Observer
     */
    public function storeDelete(Magento_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $store = $observer->getEvent()->getStore();
        /* @var $store Magento_Core_Model_Store */

        $this->_getIndexer()->deleteStore($store->getId());

        return $this;
    }

    /**
     * Store Group Save process
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Product_Flat_Observer
     */
    public function storeGroupSave(Magento_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $group = $observer->getEvent()->getGroup();
        /* @var $group Magento_Core_Model_Store_Group */

        if ($group->dataHasChangedFor('website_id')) {
            foreach ($group->getStores() as $store) {
                /* @var $store Magento_Core_Model_Store */
                $this->_getIndexer()->rebuild($store->getId());
            }
        }

        return $this;
    }

    /**
     * Catalog Product Import After process
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Product_Flat_Observer
     */
    public function catalogProductImportAfter(Magento_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $this->_getIndexer()->rebuild();

        return $this;
    }

    /**
     * Customer Group save after process
     *
     * @param Magento_Event_Observer_Collection $observer
     * @return Magento_Catalog_Model_Product_Flat_Observer
     */
    public function customerGroupSaveAfter(Magento_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $customerGroup = $observer->getEvent()->getObject();
        /* @var $customerGroup Magento_Customer_Model_Group */
        if ($customerGroup->dataHasChangedFor($customerGroup->getIdFieldName())
            || $customerGroup->dataHasChangedFor('tax_class_id')) {
            $this->_getIndexer()->updateEventAttributes();
        }
        return $this;
    }
}
