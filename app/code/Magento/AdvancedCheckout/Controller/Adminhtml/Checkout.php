<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Checkout index controller
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Controller_Adminhtml_Checkout extends Magento_Adminhtml_Controller_Action
{
    /**
     * Flag that indicates whether page must be reloaded with correct params or not
     *
     * @var bool
     */
    protected $_redirectFlag = false;


    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Return Checkout model as singleton
     *
     * @return Magento_AdvancedCheckout_Model_Cart
     */
    public function getCartModel()
    {
        return Mage::getSingleton('Magento_AdvancedCheckout_Model_Cart')
            ->setSession(Mage::getSingleton('Magento_Adminhtml_Model_Session'))
            ->setContext(Magento_AdvancedCheckout_Model_Cart::CONTEXT_ADMIN_CHECKOUT)
            ->setCurrentStore($this->getRequest()->getPost('store'));
    }

    /**
     * Init store based on quote and customer sharing options
     * Store customer, store and quote to registry
     *
     * @param bool $useRedirects
     *
     * @throws Magento_Core_Exception
     * @throws Magento_AdvancedCheckout_Exception
     * @return Magento_AdvancedCheckout_Controller_Adminhtml_Checkout
     */
    protected function _initData($useRedirects = true)
    {
        $customerId = $this->getRequest()->getParam('customer');
        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Magento_AdvancedCheckout_Exception(__('Customer not found'));
        }

        if (Mage::app()->getStore()->getWebsiteId() == $customer->getWebsiteId()) {
            if ($useRedirects) {
                $this->_getSession()->addError(
                    __('Shopping cart management disabled for this customer.')
                );
                $this->_redirect('*/customer/edit', array('id' => $customer->getId()));
                $this->_redirectFlag = true;
                return $this;
            } else {
                throw new Magento_AdvancedCheckout_Exception(
                    __('Shopping cart management is disabled for this customer.')
                );
            }
        }

        $cart = $this->getCartModel();
        $cart->setCustomer($customer);

        $storeId = $this->getRequest()->getParam('store');

        if ($storeId === null || Mage::app()->getStore($storeId)->isAdmin()) {
            $storeId = $cart->getPreferredStoreId();
            if ($storeId && $useRedirects) {
                // Redirect to preferred store view
                if ($this->getRequest()->getQuery('isAjax', false) || $this->getRequest()->getQuery('ajax', false)) {
                    $this->getResponse()->setBody(Mage::helper('Magento_Core_Helper_Data')->jsonEncode(array(
                        'url' => $this->getUrl('*/*/index', array('store' => $storeId, 'customer' => $customerId))
                    )));
                } else {
                    $this->_redirect('*/*/index', array('store' => $storeId, 'customer' => $customerId));
                }
                $this->_redirectFlag = true;
                return $this;
            } else {
                throw new Magento_AdvancedCheckout_Exception(__('We could not find this store.'));
            }
        } else {
            // try to find quote for selected store
            $cart->setStoreId($storeId);
        }

        $quote = $cart->getQuote();

        // Currency init
        if ($quote->getId()) {
            $quoteCurrencyCode = $quote->getData('quote_currency_code');
            if ($quoteCurrencyCode != Mage::app()->getStore($storeId)->getCurrentCurrencyCode()) {
                $quoteCurrency = Mage::getModel('Magento_Directory_Model_Currency')->load($quoteCurrencyCode);
                $quote->setForcedCurrency($quoteCurrency);
                Mage::app()->getStore($storeId)->setCurrentCurrencyCode($quoteCurrency->getCode());
            }
        } else {
            // customer and addresses should be set to resolve situation when no quote was saved for customer previously
            // otherwise quote would be saved with customer_id = null and zero totals
            $quote->setStore(Mage::app()->getStore($storeId))->setCustomer($customer);
            $quote->getBillingAddress();
            $quote->getShippingAddress();
            $quote->save();
        }

        $this->_coreRegistry->register('checkout_current_quote', $quote);
        $this->_coreRegistry->register('checkout_current_customer', $customer);
        $this->_coreRegistry->register('checkout_current_store', Mage::app()->getStore($storeId));

        return $this;
    }

    /**
     * Renderer for page title
     *
     * @return Magento_Adminhtml_Controller_Action
     */
    protected function _initTitle()
    {
        $this->_title(__('Customers'))->_title(__('Customers'));
        $customer = $this->_coreRegistry->registry('checkout_current_customer');
        if ($customer) {
            $this->_title($customer->getName());
        }
        $this->_title(__('Shopping Cart'));
        return $this;
    }

    /**
     * Empty page for final errors occurred
     */
    public function errorAction()
    {
        $this->loadLayout();
        $this->_initTitle();
        $this->renderLayout();
    }

    /**
     * Manage shopping cart layout
     */
    public function indexAction()
    {
        try {
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $this->loadLayout();
            $this->_initTitle();
            $this->renderLayout();
            return;
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError(
                __('An error has occurred. See error log for details.')
            );
        }
        $this->_redirect('*/*/error');
    }


    /**
     * Quote items grid ajax callback
     */
    public function cartAction()
    {
        try {
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Add products to quote, ajax
     * Currently not used, as all requests now go through loadBlock action
     */
    public function addToCartAction()
    {
        try {
            $this->_isModificationAllowed();
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }

            $cart = $this->getCartModel();
            $customer = $this->_coreRegistry->registry('checkout_current_customer');
            $store = $this->_coreRegistry->registry('checkout_current_store');

            $source = Mage::helper('Magento_Core_Helper_Data')->jsonDecode($this->getRequest()->getPost('source'));

            // Reorder products
            if (isset($source['source_ordered']) && is_array($source['source_ordered'])) {
                foreach ($source['source_ordered'] as $orderItemId => $qty) {
                    $orderItem = Mage::getModel('Magento_Sales_Model_Order_Item')->load($orderItemId);
                    $cart->reorderItem($orderItem, $qty);
                }
                unset($source['source_ordered']);
            }

            // Add new products
            if (is_array($source)) {
                foreach ($source as $products) {
                    if (is_array($products)) {
                        foreach ($products as $productId => $qty) {
                            $cart->addProduct($productId, $qty);
                        }
                    }
                }
            }

            // Collect quote totals and save it
            $cart->saveQuote();

            // Remove items from wishlist
            if (isset($source['source_wishlist']) && is_array($source['source_wishlist'])) {
                $wishlist = Mage::getModel('Magento_Wishlist_Model_Wishlist')->loadByCustomer($customer)
                    ->setStore($store)
                    ->setSharedStoreIds($store->getWebsite()->getStoreIds());
                if ($wishlist->getId()) {
                    $quoteProductIds = array();
                    foreach ($cart->getQuote()->getAllItems() as $item) {
                        $quoteProductIds[] = $item->getProductId();
                    }
                    foreach ($source['source_wishlist'] as $productId => $qty) {
                        if (in_array($productId, $quoteProductIds)) {
                            $wishlistItem = Mage::getModel('Magento_Wishlist_Model_Item')->loadByProductWishlist(
                                $wishlist->getId(),
                                $productId,
                                $wishlist->getSharedStoreIds()
                            );
                            if ($wishlistItem->getId()) {
                                $wishlistItem->delete();
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Mass update quote items, ajax
     * Currently not used, as all requests now go through loadBlock action
     */
    public function updateItemsAction()
    {
        try {
            $this->_isModificationAllowed();
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $items = $this->getRequest()->getPost('item', array());
            if ($items) {
                $this->getCartModel()->updateQuoteItems($items);
            }
            $this->getCartModel()->saveQuote();
        } catch (Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Apply/cancel coupon code in quote, ajax
     */
    public function applyCouponAction()
    {
        try {
            $this->_isModificationAllowed();
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $code = $this->getRequest()->getPost('code', '');
            $quote = $this->_coreRegistry->registry('checkout_current_quote');
            $quote->setCouponCode($code)
                ->collectTotals()
                ->save();

            $this->loadLayout();
            if (!$quote->getCouponCode()) {
                $this->getLayout()
                    ->getBlock('form_coupon')
                    ->setInvalidCouponCode($code);
            }
            $this->renderLayout();
        } catch (Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Coupon code block builder
     */
    public function couponAction()
    {
        $this->accordionAction();
    }

    /**
     * Common action for accordion grids, ajax
     */
    public function accordionAction()
    {
        try {
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Redirect to order creation page based on current quote
     */
    public function createOrderAction()
    {
        if (!$this->_authorization->isAllowed('Magento_Sales::create')) {
            Mage::throwException(__('You do not have access to this.'));
        }
        try {
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $activeQuote = $this->getCartModel()->getQuote();
            $quote = $this->getCartModel()->copyQuote($activeQuote);
            if ($quote->getId()) {
                $session = Mage::getSingleton('Magento_Adminhtml_Model_Sales_Order_Create')->getSession();
                $session->setQuoteId($quote->getId())
                   ->setStoreId($quote->getStoreId())
                   ->setCustomerId($quote->getCustomerId());

            }
            $this->_redirect('*/sales_order_create', array(
                'customer_id' => $this->_coreRegistry->registry('checkout_current_customer')->getId(),
                'store_id' => $this->_coreRegistry->registry('checkout_current_store')->getId(),
            ));
            return;
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError(
                __('An error has occurred. See error log for details.')
            );
        }
        $this->_redirect('*/*/error');
    }

    /**
     * Catalog products accordion grid callback
     */
    public function productsAction()
    {
        $this->accordionAction();
    }

    /**
     * Wishlist accordion grid callback
     */
    public function viewWishlistAction()
    {
        $this->accordionAction();
    }

    /**
     * Compared products accordion grid callback
     */
    public function viewComparedAction()
    {
        $this->accordionAction();
    }

    /**
     * Recently compared products accordion grid callback
     */
    public function viewRecentlyComparedAction()
    {
        $this->accordionAction();
    }

    /**
     * Recently viewed products accordion grid callback
     */
    public function viewRecentlyViewedAction()
    {
        $this->accordionAction();
    }

    /**
     * Last ordered items accordion grid callback
     */
    public function viewOrderedAction()
    {
        $this->accordionAction();
    }

    /**
     * Ajax handler to response configuration fieldset of composite product in order
     *
     * @return Magento_AdvancedCheckout_Controller_Adminhtml_Checkout
     */
    public function configureProductToAddAction()
    {
        $this->_initData();
        $customer   = $this->_coreRegistry->registry('checkout_current_customer');
        $store      = $this->_coreRegistry->registry('checkout_current_store');

        $storeId    = ($store instanceof Magento_Core_Model_Store) ? $store->getId() : (int) $store;
        $customerId = ($customer instanceof Magento_Customer_Model_Customer) ? $customer->getId() : (int) $customer;

        // Prepare data
        $productId  = (int)$this->getRequest()->getParam('id');

        $configureResult = new Magento_Object();
        $configureResult->setOk(true)
            ->setProductId($productId)
            ->setCurrentStoreId($storeId)
            ->setCurrentCustomerId($customerId);

        // Render page
        /* @var $helper Magento_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('Magento_Adminhtml_Helper_Catalog_Product_Composite');
        $helper->renderConfigureResult($this, $configureResult);

        return $this;
    }

    /**
     * Ajax handler to configure item in wishlist
     *
     * @return Magento_AdvancedCheckout_Controller_Adminhtml_Checkout
     */
    public function configureWishlistItemAction()
    {
        // Prepare data
        $configureResult = new Magento_Object();
        try {
            $this->_initData();

            $customer   = $this->_coreRegistry->registry('checkout_current_customer');
            $customerId = ($customer instanceof Magento_Customer_Model_Customer) ? $customer->getId() : (int) $customer;
            $store      = $this->_coreRegistry->registry('checkout_current_store');
            $storeId    = ($store instanceof Magento_Core_Model_Store) ? $store->getId() : (int) $store;

            $itemId = (int)$this->getRequest()->getParam('id');
            if (!$itemId) {
                Mage::throwException(__('The wish list item id is not received.'));
            }

            $item = Mage::getModel('Magento_Wishlist_Model_Item')
                ->loadWithOptions($itemId, 'info_buyRequest');
            if (!$item->getId()) {
                Mage::throwException(__('The wish list item is not loaded.'));
            }

            $configureResult->setOk(true)
                ->setProductId($item->getProductId())
                ->setBuyRequest($item->getBuyRequest())
                ->setCurrentStoreId($storeId)
                ->setCurrentCustomerId($customerId);
        } catch (Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        // Render page
        /* @var $helper Magento_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('Magento_Adminhtml_Helper_Catalog_Product_Composite');
        $helper->renderConfigureResult($this, $configureResult);
        return $this;
    }

    /**
     * Ajax handler to configure item in wishlist
     *
     * @return Magento_AdvancedCheckout_Controller_Adminhtml_Checkout
     */
    public function configureOrderedItemAction()
    {
        // Prepare data
        $configureResult = new Magento_Object();
        try {
            $this->_initData();

            $customer   = $this->_coreRegistry->registry('checkout_current_customer');
            $customerId = ($customer instanceof Magento_Customer_Model_Customer) ? $customer->getId() : (int) $customer;
            $store      = $this->_coreRegistry->registry('checkout_current_store');
            $storeId    = ($store instanceof Magento_Core_Model_Store) ? $store->getId() : (int) $store;

            $itemId = (int) $this->getRequest()->getParam('id');
            if (!$itemId) {
                Mage::throwException(__('Ordered item id is not received.'));
            }

            $item = Mage::getModel('Magento_Sales_Model_Order_Item')
                ->load($itemId);
            if (!$item->getId()) {
                Mage::throwException(__('Ordered item is not loaded.'));
            }

            $configureResult->setOk(true)
                ->setProductId($item->getProductId())
                ->setBuyRequest($item->getBuyRequest())
                ->setCurrentStoreId($storeId)
                ->setCurrentCustomerId($customerId);
        } catch (Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        // Render page
        /* @var $helper Magento_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('Magento_Adminhtml_Helper_Catalog_Product_Composite');
        $helper->renderConfigureResult($this, $configureResult);
        return $this;
    }

    /**
     * Process exceptions in ajax requests
     *
     * @param Exception $e
     */
    protected function _processException(Exception $e)
    {
        if ($e instanceof Magento_Core_Exception) {
            $result = array('error' => $e->getMessage());
        } elseif ($e instanceof Exception) {
            Mage::logException($e);
            $result = array(
                'error' => __('An error has occurred. See error log for details.')
            );
        }
        $this->getResponse()->setBody(Mage::helper('Magento_Core_Helper_Data')->jsonEncode($result));
    }

    /**
     * Acl check for quote modifications
     *
     * @return boolean
     */
    protected function _isModificationAllowed()
    {
        if (!$this->_authorization->isAllowed('Magento_AdvancedCheckout::update')) {
            Mage::throwException(__('You do not have access to this.'));
        }
    }

    /**
     * Acl check for admin
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_AdvancedCheckout::view')
            || $this->_authorization->isAllowed('Magento_AdvancedCheckout::update');
    }

    /**
     * Configure quote items
     *
     * @return Magento_AdvancedCheckout_Controller_Adminhtml_Checkout
     */
    public function configureQuoteItemsAction()
    {
        $this->_initData();

        // Prepare data
        $configureResult = new Magento_Object();
        try {
            $quoteItemId = (int) $this->getRequest()->getParam('id');

            if (!$quoteItemId) {
                Mage::throwException(__('Quote item id is not received.'));
            }

            $quoteItem = Mage::getModel('Magento_Sales_Model_Quote_Item')->load($quoteItemId);
            if (!$quoteItem->getId()) {
                Mage::throwException(__('Quote item is not loaded.'));
            }

            $configureResult->setOk(true);
            $optionCollection = Mage::getModel('Magento_Sales_Model_Quote_Item_Option')->getCollection()
                    ->addItemFilter(array($quoteItemId));
            $quoteItem->setOptions($optionCollection->getOptionsByItem($quoteItem));

            $configureResult->setBuyRequest($quoteItem->getBuyRequest());
            $configureResult->setCurrentStoreId($quoteItem->getStoreId());
            $configureResult->setProductId($quoteItem->getProductId());
            $sessionQuote = Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote');
            $configureResult->setCurrentCustomerId($sessionQuote->getCustomerId());
        } catch (Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        // Render page
        /* @var $helper Magento_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('Magento_Adminhtml_Helper_Catalog_Product_Composite');
        $helper->renderConfigureResult($this, $configureResult);

        return $this;
    }

    /**
     * Reload quote
     *
     * @return Magento_AdvancedCheckout_Controller_Adminhtml_Checkout
     */
    protected function _reloadQuote()
    {
        $id = $this->getCartModel()->getQuote()->getId();
        $this->getCartModel()->getQuote()->load($id);
        return $this;
    }

    /**
     * Loading page block
     */
    public function loadBlockAction()
    {
        $criticalException = false;
        try {
            $this->_initData(false)->_processData();
        } catch (Magento_AdvancedCheckout_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $criticalException = true;
        } catch (Magento_Core_Exception $e) {
            $this->_reloadQuote();
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_reloadQuote();
            $this->_getSession()->addException($e, $e->getMessage());
        }

        $asJson = $this->getRequest()->getParam('json');
        $block = $this->getRequest()->getParam('block');

        $update = $this->getLayout()->getUpdate();
        if ($asJson) {
            $update->addHandle('adminhtml_checkout_manage_load_block_json');
        } else {
            $update->addHandle('adminhtml_checkout_manage_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                if ($criticalException && ($block != 'message')) {
                    continue;
                }
                $update->addHandle('adminhtml_checkout_manage_load_block_' . $block);
            }
        }

        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        $result = $this->getLayout()->renderElement('content');
        if ($this->getRequest()->getParam('as_js_varname')) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->setUpdateResult($result);
            $this->_redirect('*/*/showUpdateResult');
        } else {
            $this->getResponse()->setBody($result);
        }
    }

    /**
     * Returns item info by list and list item id
     * Returns object on success or false on error. Returned object has following keys:
     *  - product_id - null if no item found
     *  - buy_request - Magento_Object, empty if not buy request stored for this item
     *
     * @param string $listType
     * @param int    $itemId
     *
     * @return Magento_Object|false
     */
    protected function _getListItemInfo($listType, $itemId)
    {
        $productId = null;
        $buyRequest = new Magento_Object();
        switch ($listType) {
            case 'wishlist':
                $item = Mage::getModel('Magento_Wishlist_Model_Item')
                    ->loadWithOptions($itemId, 'info_buyRequest');
                if ($item->getId()) {
                    $productId = $item->getProductId();
                    $buyRequest = $item->getBuyRequest();
                }
                break;
            case 'ordered':
                $item = Mage::getModel('Magento_Sales_Model_Order_Item')
                    ->load($itemId);
                if ($item->getId()) {
                    $productId = $item->getProductId();
                    $buyRequest = $item->getBuyRequest();
                }
                break;
            default:
                $productId = (int)$itemId;
                break;
        }

        return new Magento_Object(array('product_id' => $productId, 'buy_request' => $buyRequest));
    }

    /**
     * Wrapper for _getListItemInfo() - extends with additional list types. New method has been created to leave
     * definition of original method unchanged (add_by_sku list type utilizes additional parameter - $info).
     * @see _getListItemInfo() for return format
     *
     * @param string $listType
     * @param int    $itemId
     * @param array  $info
     * @return Magento_Object|false
     */
    protected function _getInfoForListItem($listType, $itemId, $info)
    {
        $productId = null;
        $buyRequest = new Magento_Object();
        switch ($listType) {
            case Magento_AdvancedCheckout_Block_Adminhtml_Sku_Abstract::LIST_TYPE:
                $info['sku'] = $itemId;

            case Magento_AdvancedCheckout_Block_Adminhtml_Sku_Errors_Abstract::LIST_TYPE:
                if ((!isset($info['sku'])) || (string)$info['sku'] == '') { // Allow SKU == '0'
                    return false;
                }
                $item = $this->getCartModel()->prepareAddProductBySku($info['sku'], $info['qty'], $info);
                if ($item['code'] != Magento_AdvancedCheckout_Helper_Data::ADD_ITEM_STATUS_SUCCESS) {
                    return false;
                }
                $productId = $item['item']['id'];
                break;

            default:
                return $this->_getListItemInfo($listType, $itemId);
        }
        return new Magento_Object(array('product_id' => $productId, 'buy_request' => $buyRequest));
    }

    /**
     * Processing request data
     *
     * @return Magento_AdvancedCheckout_Controller_Adminhtml_Checkout
     */
    protected function _processData()
    {
        /**
         * Update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            if ((int)$this->getRequest()->getPost('empty_customer_cart') == 1) {
                // Empty customer's shopping cart
                $this->getCartModel()->getQuote()->removeAllItems()->collectTotals()->save();
            } else {
                $items = $this->getRequest()->getPost('item', array());
                $items = $this->_processFiles($items);
                $this->getCartModel()->updateQuoteItems($items);
                if ($this->getCartModel()->getQuote()->getHasError()) {
                    foreach ($this->getCartModel()->getQuote()->getErrors() as $error) {
                        /* @var $error Magento_Core_Model_Message_Error */
                        Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($error->getCode());
                    }
                }
            }
        }

        if ($this->getRequest()->getPost('sku_remove_failed')) {
            // "Remove all" button on error grid has been pressed: remove items from "add-by-SKU" queue
            $this->getCartModel()->removeAllAffectedItems();
        }

        $sku = $this->getRequest()->getPost('remove_sku', false);
        if ($sku) {
            $this->getCartModel()->removeAffectedItem($sku);
        }

        /**
         * Add products from different lists
         */
        $listTypes = $this->getRequest()->getPost('configure_complex_list_types');
        if ($listTypes) {
            $skuListTypes = array(
                Magento_AdvancedCheckout_Block_Adminhtml_Sku_Errors_Abstract::LIST_TYPE,
                Magento_AdvancedCheckout_Block_Adminhtml_Sku_Abstract::LIST_TYPE,
            );
            /* @var $productHelper Magento_Catalog_Helper_Product */
            $productHelper = Mage::helper('Magento_Catalog_Helper_Product');
            $listTypes = array_filter(explode(',', $listTypes));
            if (in_array(Magento_AdvancedCheckout_Block_Adminhtml_Sku_Errors_Abstract::LIST_TYPE, $listTypes)) {
                // If results came from SKU error grid - clean them (submitted results are going to be re-checked)
                $this->getCartModel()->removeAllAffectedItems();
            }
            $listItems = $this->getRequest()->getPost('list');
            foreach ($listTypes as $listType) {
                if (!isset($listItems[$listType])
                    || !is_array($listItems[$listType])
                    || !isset($listItems[$listType]['item'])
                    || !is_array($listItems[$listType]['item'])
                ) {
                    continue;
                }

                $items = $listItems[$listType]['item'];

                foreach ($items as $itemId => $info) {
                    if (!is_array($info)) {
                        $info = array(); // For sure to filter incoming data
                    }

                    $itemInfo = $this->_getInfoForListItem($listType, $itemId, $info);
                    if (!$itemInfo) {
                        continue;
                    }

                    $currentConfig = $itemInfo->getBuyRequest();
                    if (isset($info['_config_absent'])) {
                        // User has added items without configuration (using multiple checkbox control)
                        // Try to use configs from list
                        if (isset($info['qty'])) {
                            $currentConfig->setQty($info['qty']);
                        }
                        $config = $currentConfig->getData();
                    } else {
                        $params = array(
                            'files_prefix' => 'list_' . $listType . '_item_' . $itemId . '_',
                            'current_config' => $currentConfig
                        );
                        $config = $productHelper->addParamsToBuyRequest($info, $params)
                            ->toArray();
                    }
                    if (in_array($listType, $skuListTypes)) {
                        // Items will be later added to cart using saveAffectedItems()
                        $this->getCartModel()->setAffectedItemConfig($itemId, $config);
                    } else {
                        try {
                            $this->getCartModel()->addProduct($itemInfo->getProductId(), $config);
                        } catch (Magento_Core_Exception $e){
                            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                        } catch (Exception $e){
                            Mage::logException($e);
                        }
                    }
                }
            }
        }


        if (is_array($listTypes) && array_intersect($listTypes, $skuListTypes)) {
            $cart = $this->getCartModel();
            // We need to save products to magento_advancedcheckout/cart instead of checkout/cart
            $cart->saveAffectedProducts($cart, false);
        }

        /**
         * Remove quote item
         */
        $removeItemId = (int)$this->getRequest()->getPost('remove_item');
        $removeFrom = (string)$this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->getCartModel()->removeItem($removeItemId, $removeFrom);
        }

        /**
         * Move quote item
         */
        $moveItemId = (int)$this->getRequest()->getPost('move_item');
        $moveTo = (string)$this->getRequest()->getPost('to');
        if ($moveItemId && $moveTo) {
            $this->getCartModel()->moveQuoteItem($moveItemId, $moveTo);
        }

        $this->getCartModel()
            ->saveQuote();

        return $this;
    }

    /**
     * Process buyRequest file options of items
     *
     * @param  array $items
     * @return array
     */
    protected function _processFiles($items)
    {
        /* @var $productHelper Magento_Catalog_Helper_Product */
        $productHelper = Mage::helper('Magento_Catalog_Helper_Product');
        foreach ($items as $id => $item) {
            $buyRequest = new Magento_Object($item);
            $params = array('files_prefix' => 'item_' . $id . '_');
            $buyRequest = $productHelper->addParamsToBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }
        return $items;
    }

    /**
     * Show item update result from loadBlockAction
     * to prevent popup alert with resend data question
     *
     */
    public function showUpdateResultAction()
    {
        $session = Mage::getSingleton('Magento_Adminhtml_Model_Session');
        if ($session->hasUpdateResult() && is_scalar($session->getUpdateResult())) {
            $this->getResponse()->setBody($session->getUpdateResult());
            $session->unsUpdateResult();
        } else {
            $session->unsUpdateResult();
            return false;
        }
    }

    /**
     * Upload and parse CSV file with SKUs and quantity
     */
    public function uploadSkuCsvAction()
    {
        try {
            $this->_initData();
        } catch (Magento_Core_Exception $e) {
            Mage::logException($e);
            $this->_redirect('*/customer');
            $this->_redirectFlag = true;
        }
        if ($this->_redirectFlag) {
            return;
        }

        /** @var $helper Magento_AdvancedCheckout_Helper_Data */
        $helper = Mage::helper('Magento_AdvancedCheckout_Helper_Data');
        $rows = $helper->isSkuFileUploaded($this->getRequest())
            ? $helper->processSkuFileUploading($this->_getSession())
            : array();

        $items = $this->getRequest()->getPost('add_by_sku');
        if (!is_array($items)) {
            $items = array();
        }
        $result = array();
        foreach ($items as $sku => $qty) {
            $result[] = array('sku' => $sku, 'qty' => $qty['qty']);
        }
        foreach ($rows as $row) {
            $result[] = $row;
        }

        if (!empty($result)) {
            $cart = $this->getCartModel();
            $cart->prepareAddProductsBySku($result);
            $cart->saveAffectedProducts($this->getCartModel(), true);
        }

        $this->_redirectReferer();
    }
}
