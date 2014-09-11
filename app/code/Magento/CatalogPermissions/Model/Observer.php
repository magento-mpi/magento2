<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Permission model
 *
 */
namespace Magento\CatalogPermissions\Model;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\CatalogPermissions\Helper\Data;
use Magento\CatalogPermissions\Model\Permission\Index;
use Magento\Framework\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Sales\Model\Quote;
use Magento\Sales\Model\Quote\Item;

class Observer
{
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
     * @var Data
     */
    protected $_catalogPermData;

    /**
     * @var Index
     */
    protected $_permissionIndex;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ConfigInterface
     */
    protected $_permissionsConfig;

    /**
     * @var ActionFlag
     */
    protected $_actionFlag;

    /**
     * @param ConfigInterface $permissionsConfig
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param Index $permissionIndex
     * @param Data $catalogPermData
     * @param ActionFlag $actionFlag
     */
    public function __construct(
        ConfigInterface $permissionsConfig,
        \Magento\Framework\StoreManagerInterface $storeManager,
        Session $customerSession,
        Index $permissionIndex,
        Data $catalogPermData,
        ActionFlag $actionFlag
    ) {
        $this->_permissionsConfig = $permissionsConfig;
        $this->_storeManager = $storeManager;
        $this->_catalogPermData = $catalogPermData;
        $this->_permissionIndex = $permissionIndex;
        $this->_customerSession = $customerSession;
        $this->_actionFlag = $actionFlag;
    }

    /**
     * Apply category permissions for category collection
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function applyCategoryPermissionOnIsActiveFilterToCollection(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        $categoryCollection = $observer->getEvent()->getCategoryCollection();

        $this->_permissionIndex->addIndexToCategoryCollection(
            $categoryCollection,
            $this->_getCustomerGroupId(),
            $this->_getWebsiteId()
        );

        return $this;
    }

    /**
     * Apply category permissions for category collection
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function applyCategoryPermissionOnLoadCollection(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        $permissions = array();
        $categoryCollection = $observer->getEvent()->getCategoryCollection();
        $categoryIds = $categoryCollection->getColumnValues('entity_id');

        if ($categoryIds) {
            $permissions = $this->_permissionIndex->getIndexForCategory(
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
     * @param EventObserver $observer
     * @return $this
     */
    public function applyCategoryInactiveIds(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        $categoryIds = $this->_permissionIndex->getRestrictedCategoryIds(
            $this->_getCustomerGroupId(),
            $this->_getWebsiteId()
        );

        $observer->getEvent()->getTree()->addInactiveCategoryIds($categoryIds);

        return $this;
    }

    /**
     * Applies category permission on model afterload
     *
     * @param EventObserver $observer
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function applyCategoryPermission(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();
        $permissions = $this->_permissionIndex->getIndexForCategory(
            $category->getId(),
            $this->_getCustomerGroupId(),
            $this->_getWebsiteId()
        );

        if (isset($permissions[$category->getId()])) {
            $category->setPermissions($permissions[$category->getId()]);
        }

        $this->_applyPermissionsOnCategory($category);
        if ($observer->getEvent()->getCategory()->getIsHidden()) {

            $observer->getEvent()->getControllerAction()->getResponse()->setRedirect(
                $this->_catalogPermData->getLandingPageUrl()
            );

            throw new \Magento\Framework\Model\Exception(__('You may need more permissions to access this category.'));
        }
        return $this;
    }

    /**
     * Apply product permissions for collection
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function applyProductPermissionOnCollection(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        $this->_permissionIndex->addIndexToProductCollection($collection, $this->_getCustomerGroupId());
        return $this;
    }

    /**
     * Apply category permissions for collection on after load
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function applyProductPermissionOnCollectionAfterLoad(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        foreach ($collection as $product) {
            if ($collection->hasFlag('product_children')) {
                $product->addData(
                    array(
                        'grant_catalog_category_view' => -1,
                        'grant_catalog_product_price' => -1,
                        'grant_checkout_items' => -1
                    )
                );
            }
            $this->_applyPermissionsOnProduct($product);
        }
        return $this;
    }

    /**
     * Checks permissions for all quote items
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function checkQuotePermissions(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
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
            /* @var $quoteItem Item */
            if ($quoteItem->getDisableAddToCart() && !$quoteItem->isDeleted()) {
                $quote->removeItem($quoteItem->getId());
                if ($parentItem) {
                    $quote->setHasError(
                        true
                    )->addMessage(
                        __('You cannot add "%1" to the cart.', $parentItem->getName())
                    );
                } else {
                    $quote->setHasError(
                        true
                    )->addMessage(
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
     * @param EventObserver $observer
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function checkQuoteItemSetProduct(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
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

        /* @var $quoteItem Item */
        if ($product->getDisableAddToCart() && !$quoteItem->isDeleted()) {
            $quoteItem->getQuote()->removeItem($quoteItem->getId());
            if ($parentItem) {
                throw new \Magento\Framework\Model\Exception(__('You cannot add "%1" to the cart.', $parentItem->getName()));
            } else {
                throw new \Magento\Framework\Model\Exception(__('You cannot add "%1" to the cart.', $quoteItem->getName()));
            }
        }

        return $this;
    }

    /**
     * Initialize permissions for quote items
     *
     * @param Quote $quote
     * @return $this
     */
    protected function _initPermissionsOnQuoteItems($quote)
    {
        $productIds = array();

        foreach ($quote->getAllItems() as $item) {
            if (!isset($this->_permissionsQuoteCache[$item->getProductId()]) && $item->getProductId()) {
                $productIds[] = $item->getProductId();
            }
        }

        if (!empty($productIds)) {
            $this->_permissionsQuoteCache += $this->_permissionIndex->getIndexForProduct(
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
            'grant_catalog_category_view' => $this->_catalogPermData->isAllowedCategoryView(),
            'grant_catalog_product_price' => $this->_catalogPermData->isAllowedProductPrice(),
            'grant_checkout_items' => $this->_catalogPermData->isAllowedCheckoutItems()
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
                    if ($permission[$grant] == -2 || $permission[$grant] != -1 && !$defaultPermission) {
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
     * @param EventObserver $observer
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function applyProductPermission(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        $this->_permissionIndex->addIndexToProduct($product, $this->_getCustomerGroupId());
        $this->_applyPermissionsOnProduct($product);
        if ($observer->getEvent()->getProduct()->getIsHidden()) {
            $observer->getEvent()->getControllerAction()->getResponse()->setRedirect(
                $this->_catalogPermData->getLandingPageUrl()
            );

            throw new \Magento\Framework\Model\Exception(__('You may need more permissions to access this product.'));
        }

        return $this;
    }

    /**
     * Apply category related permissions on category
     *
     * @param Node|Category $category
     * @return $this
     */
    protected function _applyPermissionsOnCategory($category)
    {
        if ($category->getData(
            'permissions/grant_catalog_category_view'
        ) == -2 || $category->getData(
            'permissions/grant_catalog_category_view'
        ) != -1 && !$this->_catalogPermData->isAllowedCategoryView()
        ) {
            $category->setIsActive(0);
            $category->setIsHidden(true);
        }

        return $this;
    }

    /**
     * Apply category related permissions on product
     *
     * @param Product $product
     * @return $this
     */
    protected function _applyPermissionsOnProduct($product)
    {
        if ($product->getData(
            'grant_catalog_category_view'
        ) == -2 || $product->getData(
            'grant_catalog_category_view'
        ) != -1 && !$this->_catalogPermData->isAllowedCategoryView()
        ) {
            $product->setIsHidden(true);
        }


        if ($product->getData(
            'grant_catalog_product_price'
        ) == -2 || $product->getData(
            'grant_catalog_product_price'
        ) != -1 && !$this->_catalogPermData->isAllowedProductPrice()
        ) {
            $product->setCanShowPrice(false);
            $product->setDisableAddToCart(true);
        }

        if ($product->getData(
            'grant_checkout_items'
        ) == -2 || $product->getData(
            'grant_checkout_items'
        ) != -1 && !$this->_catalogPermData->isAllowedCheckoutItems()
        ) {
            $product->setDisableAddToCart(true);
        }

        return $this;
    }

    /**
     * Apply is salable to product
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function applyIsSalableToProduct(EventObserver $observer)
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
     * @param EventObserver $observer
     * @return $this
     */
    public function checkCatalogSearchLayout(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        if (!$this->_catalogPermData->isAllowedCatalogSearch()) {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('CATALOGPERMISSIONS_DISABLED_CATALOG_SEARCH');
        }

        return $this;
    }

    /**
     * Check catalog search availability on predispatch
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function checkCatalogSearchPreDispatch(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        /** @var Action $action */
        $action = $observer->getEvent()->getControllerAction();
        if (!$this->_catalogPermData->isAllowedCatalogSearch() && !$this->_actionFlag->get(
            '',
            Action::FLAG_NO_DISPATCH
        ) && $action->getRequest()->isDispatched()
        ) {
            $this->_actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
            $action->getResponse()->setRedirect($this->_catalogPermData->getLandingPageUrl());
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
        return $this->_customerSession->getCustomerGroupId();
    }

    /**
     * Retrieve current website id
     *
     * @return int
     */
    protected function _getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * Apply catalog permissions on product RSS feeds
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function checkIfProductAllowedInRss(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        $row = $observer->getEvent()->getRow();
        if (!$row) {
            $row = $observer->getEvent()->getProduct()->getData();
        }

        $observer->getEvent()->getProduct()->setAllowedInRss(
            $this->_checkPermission($row, 'grant_catalog_category_view', 'isAllowedCategoryView')
        );

        $observer->getEvent()->getProduct()->setAllowedPriceInRss(
            $this->_checkPermission($row, 'grant_catalog_product_price', 'isAllowedProductPrice')
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
     * @param string $method method name from Data class
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

        if (!$this->_catalogPermData->{$method}()) {
            if ($data[$permission] == Permission::PERMISSION_ALLOW) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            if ($data[$permission] != Permission::PERMISSION_DENY || is_null($data[$permission])) {
                $result = true;
            } else {
                $result = false;
            }
        }

        return $result;
    }
}
