<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Permission model
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
class Magento_CatalogPermissions_Model_Observer
{
    const XML_PATH_GRANT_CATALOG_CATEGORY_VIEW = 'catalog/magento_catalogpermissions/grant_catalog_category_view';
    const XML_PATH_GRANT_CATALOG_PRODUCT_PRICE = 'catalog/magento_catalogpermissions/grant_catalog_product_price';
    const XML_PATH_GRANT_CHECKOUT_ITEMS = 'catalog/magento_catalogpermissions/grant_checkout_items';

    /**
     * Is in product queue flag
     *
     * @var boolean
     */
    protected $_isProductQueue = false;

    /**
     * Is in category queue flag
     *
     * @var boolean
     */
    protected $_isCategoryQueue = false;

    /**
     * Models queue for permission apling
     *
     * @var array
     */
    protected $_queue = array();

    /**
     * Permissions cache for products in cart
     *
     * @var array
     */
    protected $_permissionsQuoteCache = array();

    /**
     * Catalog permission helper
     *
     * @var Magento_CatalogPermissions_Helper_Data
     */
    protected $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('Magento_CatalogPermissions_Helper_Data');
    }

    /**
     * Apply category permissions for category collection
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function applyCategoryPermissionOnIsActiveFilterToCollection(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $categoryCollection = $observer->getEvent()->getCategoryCollection();

        $this->_getIndexModel()->addIndexToCategoryCollection(
            $categoryCollection,
            $this->_getCustomerGroupId(),
            $this->_getWebsiteId()
        );

        return $this;
    }

    /**
     * Apply category permissions for category collection
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function applyCategoryPermissionOnLoadCollection(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $permissions = array();
        $categoryCollection = $observer->getEvent()->getCategoryCollection();
        $categoryIds = $categoryCollection->getColumnValues('entity_id');

        if ($categoryIds) {
            $permissions = $this->_getIndexModel()->getIndexForCategory(
                $categoryIds,
                $this->_getCustomerGroupId(),
                $this->_getWebsiteId()
            );
        }

        foreach ($permissions as $categoryId => $permission) {
            $categoryCollection->getItemById($categoryId)->setPermissions($permission);
        }

        foreach ($categoryCollection as $category) {
            $this->_applyPermissionsOnCategory($category);
        }

        return $this;
    }

    /**
     * Apply category view for tree
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function applyCategoryInactiveIds(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $categoryIds = $this->_getIndexModel()->getRestrictedCategoryIds(
            $this->_getCustomerGroupId(),
            $this->_getWebsiteId()
        );

        $observer->getEvent()->getTree()->addInactiveCategoryIds($categoryIds);

        return $this;
    }

    /**
     * Applies permissions on product count for categories
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function applyCategoryPermissionOnProductCount(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        $this->_getIndexModel()->addIndexToProductCount($collection, $this->_getCustomerGroupId());
        return $this;
    }

    /**
     * Applies category permission on model afterload
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function applyCategoryPermission(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();
        $permissions = $this->_getIndexModel()->getIndexForCategory(
            $category->getId(),
            $this->_getCustomerGroupId(),
            $this->_getWebsiteId()
        );

        if (isset($permissions[$category->getId()])) {
            $category->setPermissions($permissions[$category->getId()]);
        }

        $this->_applyPermissionsOnCategory($category);
        if ($observer->getEvent()->getCategory()->getIsHidden()) {

            $observer->getEvent()->getControllerAction()->getResponse()
                ->setRedirect($this->_helper->getLandingPageUrl());

            Mage::throwException(
                __('You may need more permissions to access this category.')
            );
        }
        return $this;
    }

    /**
     * Apply product permissions for collection
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function applyProductPermissionOnCollection(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        $this->_getIndexModel()->addIndexToProductCollection($collection, $this->_getCustomerGroupId());
        return $this;
    }

    /**
     * Apply category permissions for collection on after load
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function applyProductPermissionOnCollectionAfterLoad(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        foreach ($collection as $product) {
            if ($collection->hasFlag('product_children')) {
                $product->addData(array(
                    'grant_catalog_category_view'   => -1,
                    'grant_catalog_product_price'   => -1,
                    'grant_checkout_items'          => -1,
                ));
            }
            $this->_applyPermissionsOnProduct($product);
        }
        return $this;
    }

    /**
     * Checks permissions for all quote items
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function checkQuotePermissions(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $quote = $observer->getEvent()->getCart()->getQuote();
        $this->_initPermissionsOnQuoteItems($quote);

        foreach ($quote->getAllItems() as $quoteItem) {
            if ($quoteItem->getParentItem()) {
                $parentItem = $quoteItem->getParentItem();
            } else {
                $parentItem = false;
            }
            /* @var $quoteItem Magento_Sales_Model_Quote_Item */
            if ($quoteItem->getDisableAddToCart() && !$quoteItem->isDeleted()) {
                $quote->removeItem($quoteItem->getId());
                if ($parentItem) {
                    $quote->setHasError(true)
                            ->addMessage(
                                __('You cannot add "%1" to the cart.', $parentItem->getName())
                            );
                } else {
                     $quote->setHasError(true)
                            ->addMessage(
                                __('You cannot add "%1" to the cart.', $quoteItem->getName())
                            );
                }
            }
        }

        return $this;
    }

    /**
     * Checks quote item for product permissions
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function checkQuoteItemSetProduct(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $quoteItem = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();

        if ($quoteItem->getId()) {
            return $this;
        }

        if ($quoteItem->getParentItem()) {
            $parentItem = $quoteItem->getParentItem();
        } else {
            $parentItem = false;
        }

        /* @var $quoteItem Magento_Sales_Model_Quote_Item */
        if ($product->getDisableAddToCart() && !$quoteItem->isDeleted()) {
            $quoteItem->getQuote()->removeItem($quoteItem->getId());
            if ($parentItem) {
                Mage::throwException(
                    __('You cannot add "%1" to the cart.', $parentItem->getName())
                );
            } else {
                Mage::throwException(
                    __('You cannot add "%1" to the cart.', $quoteItem->getName())
                );
            }
        }

        return $this;
    }

    /**
     * Initialize permissions for quote items
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return Magento_CatalogPermissions_Model_Observer
     */
    protected function _initPermissionsOnQuoteItems($quote)
    {
        $productIds = array();

        foreach ($quote->getAllItems() as $item) {
            if (!isset($this->_permissionsQuoteCache[$item->getProductId()]) &&
                $item->getProductId()) {
                $productIds[] = $item->getProductId();
            }
        }

        if (!empty($productIds)) {
            $this->_permissionsQuoteCache += $this->_getIndexModel()->getIndexForProduct(
                $productIds,
                $this->_getCustomerGroupId(),
                $quote->getStoreId()
            );

            foreach ($productIds as $productId) {
                if (!isset($this->_permissionsQuoteCache[$productId])) {
                    $this->_permissionsQuoteCache[$productId] = false;
                }
            }
        }

        $defaultGrants = array(
            'grant_catalog_category_view' => $this->_helper->isAllowedCategoryView(),
            'grant_catalog_product_price' => $this->_helper->isAllowedProductPrice(),
            'grant_checkout_items' => $this->_helper->isAllowedCheckoutItems()
        );

        foreach ($quote->getAllItems() as $item) {
            if ($item->getProductId()) {
                $permission = $this->_permissionsQuoteCache[$item->getProductId()];
                if (!$permission && in_array(false, $defaultGrants)) {
                    // If no permission found, and no one of default grant is disallowed
                    $item->setDisableAddToCart(true);
                    continue;
                }

                foreach ($defaultGrants as $grant => $defaultPermission) {
                    if ($permission[$grant] == -2 ||
                        ($permission[$grant] != -1 && !$defaultPermission)) {
                        $item->setDisableAddToCart(true);
                        break;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Apply product permissions on model after load
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function applyProductPermission(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        $this->_getIndexModel()->addIndexToProduct($product, $this->_getCustomerGroupId());
        $this->_applyPermissionsOnProduct($product);
        if ($observer->getEvent()->getProduct()->getIsHidden()) {
            $observer->getEvent()->getControllerAction()->getResponse()
                ->setRedirect($this->_helper->getLandingPageUrl());

            Mage::throwException(
                __('You may need more permissions to access this product.')
            );
        }

        return $this;
    }

    /**
     * Apply category related permissions on category
     *
     * @param \Magento\Data\Tree\Node|Magento_Catalog_Model_Category
     * @return Magento_CatalogPermissions_Model_Observer
     */
    protected function _applyPermissionsOnCategory($category)
    {
        if ($category->getData('permissions/grant_catalog_category_view') == -2 ||
            ($category->getData('permissions/grant_catalog_category_view')!= -1 &&
                !$this->_helper->isAllowedCategoryView())) {
            $category->setIsActive(0);
            $category->setIsHidden(true);
        }

        return $this;
    }

    /**
     * Apply category related permissions on product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_CatalogPermissions_Model_Observer
     */
    protected function _applyPermissionsOnProduct($product)
    {
        if ($product->getData('grant_catalog_category_view') == -2 ||
            ($product->getData('grant_catalog_category_view')!= -1 &&
                !$this->_helper->isAllowedCategoryView())) {
            $product->setIsHidden(true);
        }


        if ($product->getData('grant_catalog_product_price') == -2 ||
            ($product->getData('grant_catalog_product_price')!= -1 &&
                !$this->_helper->isAllowedProductPrice())) {
            $product->setCanShowPrice(false);
            $product->setDisableAddToCart(true);
        }

        if ($product->getData('grant_checkout_items') == -2 ||
            ($product->getData('grant_checkout_items')!= -1 &&
                !$this->_helper->isAllowedCheckoutItems())) {
            $product->setDisableAddToCart(true);
        }

        return $this;
    }

    /**
     * Apply is salable to product
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function applyIsSalableToProduct(\Magento\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getDisableAddToCart()) {
            $observer->getEvent()->getSalable()->setIsSalable(false);
        }
        return $this;
    }


    /**
     * Check catalog search availability on load layout
     *
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function checkCatalogSearchLayout(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        if (!$this->_helper->isAllowedCatalogSearch()) {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle(
                'CATALOGPERMISSIONS_DISABLED_CATALOG_SEARCH'
            );
        }

        return $this;
    }

    /**
     * Check catalog search availability on predispatch
     *
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function checkCatalogSearchPreDispatch(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $action = $observer->getEvent()->getControllerAction();
        if (!$this->_helper->isAllowedCatalogSearch()
            && !$action->getFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH)
            && $action->getRequest()->isDispatched()
        ) {
            $action->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            $action->getResponse()->setRedirect($this->_helper->getLandingPageUrl());
        }

        return $this;
    }

    /**
     * Retrieve current customer group id
     *
     * @return int
     */
    protected function _getCustomerGroupId()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId();
    }

    /**
     * Retrieve permission index model
     *
     * @return Magento_CatalogPermissions_Model_Permission_Index
     */
    protected function _getIndexModel()
    {
        return Mage::getSingleton('Magento_CatalogPermissions_Model_Permission_Index');
    }

    /**
     * Retrieve current website id
     *
     * @return int
     */
    protected function _getWebsiteId()
    {
        return Mage::app()->getStore()->getWebsiteId();
    }

    /**
     * Apply catalog permissions on product RSS feeds
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogPermissions_Model_Observer
     */
    public function checkIfProductAllowedInRss(\Magento\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return $this;
        }

        $row = $observer->getEvent()->getRow();
        if (!$row) {
            $row = $observer->getEvent()->getProduct()->getData();
        }

        $observer->getEvent()->getProduct()->setAllowedInRss(
            $this->_checkPermission(
                $row,
                'grant_catalog_category_view',
                'isAllowedCategoryView'
            )
        );

        $observer->getEvent()->getProduct()->setAllowedPriceInRss(
            $this->_checkPermission(
                $row,
                'grant_catalog_product_price',
                'isAllowedProductPrice'
            )
        );

        return $this;
    }

    /**
     * Checks permission in passed product data.
     * For retrieving default configuration value used
     * $method from helper magento_catalogpermissions.
     *
     * @param array $data
     * @param string $permission
     * @param string $method method name from Magento_CatalogPermissions_Helper_Data class
     * @return bool
     */
    protected function _checkPermission($data, $permission, $method)
    {
        $result = true;

        /*
         * If there is no permissions for this
         * product then we will use configuration default
         */
        if (!array_key_exists($permission, $data)) {
            $data[$permission] = null;
        }

        if (!$this->_helper->$method()) {
            if ($data[$permission] == Magento_CatalogPermissions_Model_Permission::PERMISSION_ALLOW) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            if ($data[$permission] != Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY
                    || is_null($data[$permission])) {
                $result = true;
            } else {
                $result = false;
            }
        }

        return $result;
    }

}
