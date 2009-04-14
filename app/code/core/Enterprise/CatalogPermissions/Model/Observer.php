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
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Permission model
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Model_Observer
{
    const XML_PATH_GRANT_CATALOG_CATEGORY_VIEW = 'enterprise_catalogpermissions/general/grant_catalog_category_view';
    const XML_PATH_GRANT_CATALOG_PRODUCT_PRICE = 'enterprise_catalogpermissions/general/grant_catalog_product_price';
    const XML_PATH_GRANT_CHECKOUT_ITEMS = 'enterprise_catalogpermissions/general/grant_checkout_items';



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
     * Apply category permissions for category collection
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function applyCategoryPermissionOnIsActiveFilterToCollection(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $categoryCollection = $observer->getEvent()->getCategoryCollection();

        $this->_getIndexModel()->addIndexToCategoryCollection($categoryCollection, $this->_getCustomerGroupId(), $this->_getWebsiteId());
        return $this;
    }


    /**
     * Apply category permissions for category collection
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function applyCategoryPermissionOnLoadCollection(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }


        $categoryCollection = $observer->getEvent()->getCategoryCollection();
        $categoryIds = $categoryCollection->getColumnValues('entity_id');
        $permissions = $this->_getIndexModel()->getIndexForCategory($categoryIds, $this->_getCustomerGroupId(), $this->_getWebsiteId());

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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function applyCategoryInactiveIds(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $categoryIds = $this->_getIndexModel()->getRestrictedCategoryIds($this->_getCustomerGroupId(), $this->_getWebsiteId());

        $observer->getEvent()->getTree()->addInactiveCategoryIds($categoryIds);

        return $this;
    }

    /**
     * Apply category view for tree
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function applyPriceGrantOnPriceIndex(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $this->_getIndexModel()->applyPriceGrantToPriceIndex($observer->getEvent(), $this->_getCustomerGroupId(), $this->_getWebsiteId());
        return $this;
    }

    /**
     * Applies permissions on product count for categories
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function applyCategoryPermissionOnProductCount(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        $this->_getIndexModel()->addIndexToProductCount($collection, $this->_getCustomerGroupId());
        return $this;
    }

    /**
     * Applies category permission on model afterload
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function applyCategoryPermission(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();
        $permissions = $this->_getIndexModel()->getIndexForCategory($category->getId(), $this->_getCustomerGroupId(), $this->_getWebsiteId());

        if (isset($permissions[$category->getId()])) {
            $category->setPermissions($permissions[$category->getId()]);
        }

        $this->_applyPermissionsOnCategory($category);
        if ($observer->getEvent()->getCategory()->getIsHidden()) {

            $observer->getEvent()->getControllerAction()->getResponse()
                ->setRedirect(Mage::helper('enterprise_catalogpermissions')->getLandingPageUrl());

            Mage::throwException(Mage::helper('enterprise_catalogpermissions')->__('You have no permissions to access this category'));
        }
        return $this;
    }

    /**
     * Apply product permissions for collection
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function applyProductPermissionOnCollection(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        $this->_getIndexModel()->addIndexToProductCollection($collection, $this->_getCustomerGroupId());
        return $this;
    }

    /**
     * Apply category permissions for collection on after load
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function applyProductPermissionOnCollectionAfterLoad(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        foreach ($collection as $product) {
            $this->_applyPermissionsOnProduct($product);
        }
        return $this;
    }

    /**
     * Checks quote item for product permissions
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function checkQuoteItem(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $quoteItem = $observer->getEvent()->getItem();

        $this->_initPermissionsOnQuoteItems($quoteItem->getQuote());

        if ($quoteItem->getParentItem()) {
            $parentItem = $quoteItem->getParentItem();
        } else {
            $parentItem = false;
        }

        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
        if ($quoteItem->getDisableAddToCart() && !$quoteItem->isDeleted()) {
            $quoteItem->getQuote()->removeItem($quoteItem->getId());
            if ($parentItem) {
                $quoteItem->getQuote()->setHasError(true)
                        ->addMessage(
                            Mage::helper('enterprise_catalogpermissions')->__('You cannot add product "%s" to cart.', $parentItem->getName())
                        );
            } else {
                 $quoteItem->getQuote()->setHasError(true)
                        ->addMessage(
                            Mage::helper('enterprise_catalogpermissions')->__('You cannot add product "%s" to cart.', $quoteItem->getName())
                        );
            }
        }

        return $this;
    }

    /**
     * Checks quote item for product permissions
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function checkQuoteItemSetProduct(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
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

        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
        if ($product->getDisableAddToCart() && !$quoteItem->isDeleted()) {
            $quoteItem->getQuote()->removeItem($quoteItem->getId());
            if ($parentItem) {
                Mage::throwException(
                    Mage::helper('enterprise_catalogpermissions')->__('You cannot add product "%s" to cart.', $parentItem->getName())
                );
            } else {
                Mage::throwException(
                            Mage::helper('enterprise_catalogpermissions')->__('You cannot add product "%s" to cart.', $quoteItem->getName())
                );
            }
        }

        return $this;
    }

    /**
     * Initialize permissions for quote items
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Enterprise_CatalogPermissions_Model_Observer
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
            'grant_catalog_category_view' => Mage::helper('enterprise_catalogpermissions')->isAllowedCategoryView(),
            'grant_catalog_product_price' => Mage::helper('enterprise_catalogpermissions')->isAllowedProductPrice(),
            'grant_checkout_items' => Mage::helper('enterprise_catalogpermissions')->isAllowedCheckoutItems()
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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function applyProductPermission(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        $this->_getIndexModel()->addIndexToProduct($product, $this->_getCustomerGroupId());
        $this->_applyPermissionsOnProduct($product);
        if ($observer->getEvent()->getProduct()->getIsHidden()) {
            $observer->getEvent()->getControllerAction()->getResponse()
                ->setRedirect(Mage::helper('enterprise_catalogpermissions')->getLandingPageUrl());

            Mage::throwException(Mage::helper('enterprise_catalogpermissions')->__('You have no permissions to access this product'));
        }

        return $this;
    }




    /**
     * Apply category related permissions on category
     *
     * @param Varien_Data_Tree_Node|Mage_Catalog_Model_Category
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    protected function _applyPermissionsOnCategory($category)
    {
        if ($category->getData('permissions/grant_catalog_category_view') == -2 ||
            ($category->getData('permissions/grant_catalog_category_view')!= -1 &&
                !Mage::helper('enterprise_catalogpermissions')->isAllowedCategoryView())) {
            $category->setIsActive(0);
            $category->setIsHidden(true);
        }

        return $this;
    }



    /**
     * Apply category related permissions on product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    protected function _applyPermissionsOnProduct($product)
    {
        if ($product->getData('grant_catalog_category_view') == -2 ||
            ($product->getData('grant_catalog_category_view')!= -1 &&
                !Mage::helper('enterprise_catalogpermissions')->isAllowedCategoryView())) {
            $product->setIsHidden(true);
        }


        if ($product->getData('grant_catalog_product_price') == -2 ||
            ($product->getData('grant_catalog_product_price')!= -1 &&
                !Mage::helper('enterprise_catalogpermissions')->isAllowedProductPrice())) {
            $product->setCanShowPrice(false);
            $product->setDisableAddToCart(true);
        }

        if ($product->getData('grant_checkout_items') == -2 ||
            ($product->getData('grant_checkout_items')!= -1 &&
                !Mage::helper('enterprise_catalogpermissions')->isAllowedCheckoutItems())) {
            $product->setDisableAddToCart(true);
        }

        return $this;
    }

    /**
     * Apply is salable to product
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function applyIsSalableToProduct(Varien_Event_Observer $observer)
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
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function checkCatalogSearchLayout(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        if (!Mage::helper('enterprise_catalogpermissions')->isAllowedCatalogSearch()) {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle(
                'CATALOGPERMISSIONS_DISABLED_CATALOG_SEARCH'
            );
        }

        return $this;
    }

    /**
     * Check catalog search availability on predispatch
     *
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    public function checkCatalogSearchPreDispatch(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        if (!Mage::helper('enterprise_catalogpermissions')->isAllowedCatalogSearch()
            && !$observer->getEvent()->getControllerAction()->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true)
            && $observer->getEvent()->getControllerAction()->getRequest()->isDispatched()) {
            $observer->getEvent()->getControllerAction()->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            $observer->getEvent()->getControllerAction()->getResponse()
                ->setRedirect(Mage::helper('enterprise_catalogpermissions')->getLandingPageUrl());
        }

        return $this;
    }

    /**
     * Retreive current customer group id
     *
     * @return int
     */
    protected function _getCustomerGroupId()
    {
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    /**
     * Retreive permission index model
     *
     * @return Enterprise_CatalogPermissions_Model_Permission_Index
     */
    protected function _getIndexModel()
    {
        return Mage::getSingleton('enterprise_catalogpermissions/permission_index');
    }

    /**
     * Retreive current website id
     *
     * @return int
     */
    protected function _getWebsiteId()
    {
        return Mage::app()->getStore()->getWebsiteId();
    }
}