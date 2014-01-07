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
namespace Magento\AdvancedCheckout\Controller\Adminhtml;


use Magento\Backend\App\Action;

class Index extends \Magento\Backend\App\Action
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
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $registry
    ) {
        parent::__construct($context);
        $this->_registry = $registry;
    }

    /**
     * Return Checkout model as singleton
     *
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function getCartModel()
    {
        return $this->_objectManager->get('Magento\AdvancedCheckout\Model\Cart')
            ->setSession($this->_objectManager->get('Magento\Backend\Model\Session'))
            ->setContext(\Magento\AdvancedCheckout\Model\Cart::CONTEXT_ADMIN_CHECKOUT)
            ->setCurrentStore($this->getRequest()->getPost('store'));
    }

    /**
     * Init store based on quote and customer sharing options
     * Store customer, store and quote to registry
     *
     * @param bool $useRedirects
     *
     * @throws \Magento\Core\Exception
     * @throws \Magento\AdvancedCheckout\Exception
     * @return \Magento\AdvancedCheckout\Controller\Adminhtml\Index
     */
    protected function _initData($useRedirects = true)
    {
        $customerId = $this->getRequest()->getParam('customer');
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        if (!$customer->getId()) {
            throw new \Magento\AdvancedCheckout\Exception(__('Customer not found'));
        }

        $storeManager = $this->_objectManager->get('Magento\Core\Model\StoreManager');
        if ($storeManager->getStore()->getWebsiteId() == $customer->getWebsiteId()) {
            if ($useRedirects) {
                $this->messageManager->addError(
                    __('Shopping cart management disabled for this customer.')
                );
                $this->_redirect('customer/index/edit', array('id' => $customer->getId()));
                $this->_redirectFlag = true;
                return $this;
            } else {
                throw new \Magento\AdvancedCheckout\Exception(
                    __('Shopping cart management is disabled for this customer.')
                );
            }
        }

        $cart = $this->getCartModel();
        $cart->setCustomer($customer);

        $storeId = $this->getRequest()->getParam('store');

        if ($storeId === null || $storeId == \Magento\Core\Model\Store::DEFAULT_STORE_ID) {
            $storeId = $cart->getPreferredStoreId();
            if ($storeId && $useRedirects) {
                // Redirect to preferred store view
                if ($this->getRequest()->getQuery('isAjax', false) || $this->getRequest()->getQuery('ajax', false)) {
                    $this->getResponse()->setBody(
                        $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(
                            array(
                                'url' => $this->getUrl(
                                    '*/*/index',
                                    array('store' => $storeId, 'customer' => $customerId)
                                )
                            )
                        )
                    );
                } else {
                    $this->_redirect('checkout/*/index', array('store' => $storeId, 'customer' => $customerId));
                }
                $this->_redirectFlag = true;
                return $this;
            } else {
                throw new \Magento\AdvancedCheckout\Exception(__('We could not find this store.'));
            }
        } else {
            // try to find quote for selected store
            $cart->setStoreId($storeId);
        }

        $quote = $cart->getQuote();

        // Currency init
        if ($quote->getId()) {
            $quoteCurrencyCode = $quote->getData('quote_currency_code');
            if ($quoteCurrencyCode != $storeManager->getStore($storeId)->getCurrentCurrencyCode()) {
                $quoteCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                    ->load($quoteCurrencyCode);
                $quote->setForcedCurrency($quoteCurrency);
                $storeManager->getStore($storeId)->setCurrentCurrencyCode($quoteCurrency->getCode());
            }
        } else {
            // customer and addresses should be set to resolve situation when no quote was saved for customer previously
            // otherwise quote would be saved with customer_id = null and zero totals
            $quote->setStore($storeManager->getStore($storeId))->setCustomer($customer);
            $quote->getBillingAddress();
            $quote->getShippingAddress();
            $quote->save();
        }

        $this->_registry->register('checkout_current_quote', $quote);
        $this->_registry->register('checkout_current_customer', $customer);
        $this->_registry->register('checkout_current_store', $storeManager->getStore($storeId));

        return $this;
    }

    /**
     * Renderer for page title
     *
     * @return \Magento\Backend\App\Action
     */
    protected function _initTitle()
    {
        $this->_title->add(__('Customers'));
        $this->_title->add(__('Customers'));
        $customer = $this->_registry->registry('checkout_current_customer');
        if ($customer) {
            $this->_title->add($customer->getName());
        }
        $itemsBlock = $this->_view->getLayout()->getBlock('ID');
        if (is_object($itemsBlock) && method_exists($itemsBlock, 'getHeaderText')) {
            $this->_title->add($itemsBlock->getHeaderText());
        } else {
            $this->_title->add(__('Shopping Cart'));
        }
        return $this;
    }

    /**
     * Empty page for final errors occurred
     */
    public function errorAction()
    {
        $this->_view->loadLayout();
        $this->_initTitle();
        $this->_view->renderLayout();
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
            $this->_view->loadLayout();
            $this->_initTitle();
            $this->_view->renderLayout();
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->messageManager->addError(
                __('An error has occurred. See error log for details.')
            );
        }
        $this->_redirect('checkout/*/error');
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
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } catch (\Exception $e) {
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
            $customer = $this->_registry->registry('checkout_current_customer');
            $store = $this->_registry->registry('checkout_current_store');

            $source = $this->_objectManager->get('Magento\Core\Helper\Data')
                ->jsonDecode($this->getRequest()->getPost('source'));

            // Reorder products
            if (isset($source['source_ordered']) && is_array($source['source_ordered'])) {
                foreach ($source['source_ordered'] as $orderItemId => $qty) {
                    $orderItem = $this->_objectManager->create('Magento\Sales\Model\Order\Item')->load($orderItemId);
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
                $wishlist = $this->_objectManager->create('Magento\Wishlist\Model\Wishlist')->loadByCustomer($customer)
                    ->setStore($store)
                    ->setSharedStoreIds($store->getWebsite()->getStoreIds());
                if ($wishlist->getId()) {
                    $quoteProductIds = array();
                    foreach ($cart->getQuote()->getAllItems() as $item) {
                        $quoteProductIds[] = $item->getProductId();
                    }
                    foreach ($source['source_wishlist'] as $productId => $qty) {
                        if (in_array($productId, $quoteProductIds)) {
                            $wishlistItem = $this->_objectManager->create('Magento\Wishlist\Model\Item')
                                ->loadByProductWishlist(
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
            $quote = $this->_registry->registry('checkout_current_quote');
            $quote->setCouponCode($code)
                ->collectTotals()
                ->save();

            $this->_view->loadLayout();
            if (!$quote->getCouponCode()) {
                $this->_view->getLayout()
                    ->getBlock('form_coupon')
                    ->setInvalidCouponCode($code);
            }
            $this->_view->renderLayout();
        } catch (\Exception $e) {
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
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } catch (\Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Redirect to order creation page based on current quote
     */
    public function createOrderAction()
    {
        if (!$this->_authorization->isAllowed('Magento_Sales::create')) {
            throw new \Magento\Core\Exception(__('You do not have access to this.'));
        }
        try {
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $activeQuote = $this->getCartModel()->getQuote();
            $quote = $this->getCartModel()->copyQuote($activeQuote);
            if ($quote->getId()) {
                $session = $this->_objectManager->get('Magento\Sales\Model\AdminOrder\Create')->getSession();
                $session->setQuoteId($quote->getId())
                   ->setStoreId($quote->getStoreId())
                   ->setCustomerId($quote->getCustomerId());

            }
            $this->_redirect('sales/order_create', array(
                'customer_id' => $this->_registry->registry('checkout_current_customer')->getId(),
                'store_id' => $this->_registry->registry('checkout_current_store')->getId(),
            ));
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->messageManager->addError(
                __('An error has occurred. See error log for details.')
            );
        }
        $this->_redirect('checkout/*/error');
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
     * @return \Magento\AdvancedCheckout\Controller\Adminhtml\Index
     */
    public function configureProductToAddAction()
    {
        $this->_initData();
        $customer   = $this->_registry->registry('checkout_current_customer');
        $store      = $this->_registry->registry('checkout_current_store');

        $storeId    = ($store instanceof \Magento\Core\Model\Store) ? $store->getId() : (int) $store;
        $customerId = ($customer instanceof \Magento\Customer\Model\Customer) ? $customer->getId() : (int) $customer;

        // Prepare data
        $productId  = (int)$this->getRequest()->getParam('id');

        $configureResult = new \Magento\Object();
        $configureResult->setOk(true)
            ->setProductId($productId)
            ->setCurrentStoreId($storeId)
            ->setCurrentCustomerId($customerId);

        // Render page
        /* @var $helper \Magento\Catalog\Helper\Product\Composite */
        $helper = $this->_objectManager->get('Magento\Catalog\Helper\Product\Composite');
        $helper->renderConfigureResult($configureResult);
    }

    /**
     * Ajax handler to configure item in wishlist
     *
     * @return \Magento\AdvancedCheckout\Controller\Adminhtml\Index
     */
    public function configureWishlistItemAction()
    {
        // Prepare data
        $configureResult = new \Magento\Object();
        try {
            $this->_initData();

            $customer   = $this->_registry->registry('checkout_current_customer');
            $customerId = ($customer instanceof \Magento\Customer\Model\Customer) ? $customer->getId() : (int) $customer;
            $store      = $this->_registry->registry('checkout_current_store');
            $storeId    = ($store instanceof \Magento\Core\Model\Store) ? $store->getId() : (int) $store;

            $itemId = (int)$this->getRequest()->getParam('id');
            if (!$itemId) {
                throw new \Magento\Core\Exception(__('The wish list item id is not received.'));
            }

            $item = $this->_objectManager->create('Magento\Wishlist\Model\Item')
                ->loadWithOptions($itemId, 'info_buyRequest');
            if (!$item->getId()) {
                throw new \Magento\Core\Exception(__('The wish list item is not loaded.'));
            }

            $configureResult->setOk(true)
                ->setProductId($item->getProductId())
                ->setBuyRequest($item->getBuyRequest())
                ->setCurrentStoreId($storeId)
                ->setCurrentCustomerId($customerId);
        } catch (\Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        // Render page
        /* @var $helper \Magento\Catalog\Helper\Product\Composite */
        $helper = $this->_objectManager->get('Magento\Catalog\Helper\Product\Composite');
        $helper->renderConfigureResult($configureResult);
    }

    /**
     * Ajax handler to configure item in wishlist
     *
     * @return \Magento\AdvancedCheckout\Controller\Adminhtml\Index
     */
    public function configureOrderedItemAction()
    {
        // Prepare data
        $configureResult = new \Magento\Object();
        try {
            $this->_initData();

            $customer   = $this->_registry->registry('checkout_current_customer');
            $customerId = ($customer instanceof \Magento\Customer\Model\Customer) ? $customer->getId() : (int) $customer;
            $store      = $this->_registry->registry('checkout_current_store');
            $storeId    = ($store instanceof \Magento\Core\Model\Store) ? $store->getId() : (int) $store;

            $itemId = (int) $this->getRequest()->getParam('id');
            if (!$itemId) {
                throw new \Magento\Core\Exception(__('Ordered item id is not received.'));
            }

            $item = $this->_objectManager->create('Magento\Sales\Model\Order\Item')
                ->load($itemId);
            if (!$item->getId()) {
                throw new \Magento\Core\Exception(__('Ordered item is not loaded.'));
            }

            $configureResult->setOk(true)
                ->setProductId($item->getProductId())
                ->setBuyRequest($item->getBuyRequest())
                ->setCurrentStoreId($storeId)
                ->setCurrentCustomerId($customerId);
        } catch (\Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        // Render page
        /* @var $helper \Magento\Catalog\Helper\Product\Composite */
        $helper = $this->_objectManager->get('Magento\Catalog\Helper\Product\Composite');
        $helper->renderConfigureResult($configureResult);
    }

    /**
     * Process exceptions in ajax requests
     *
     * @param \Exception $e
     */
    protected function _processException(\Exception $e)
    {
        if ($e instanceof \Magento\Core\Exception) {
            $result = array('error' => $e->getMessage());
        } elseif ($e instanceof \Exception) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $result = array(
                'error' => __('An error has occurred. See error log for details.')
            );
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
    }

    /**
     * Acl check for quote modifications
     *
     * @throws \Magento\Core\Exception
     * @return boolean
     */
    protected function _isModificationAllowed()
    {
        if (!$this->_authorization->isAllowed('Magento_AdvancedCheckout::update')) {
            throw new \Magento\Core\Exception(__('You do not have access to this.'));
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
     * @return \Magento\AdvancedCheckout\Controller\Adminhtml\Index
     */
    public function configureQuoteItemsAction()
    {
        $this->_initData();

        // Prepare data
        $configureResult = new \Magento\Object();
        try {
            $quoteItemId = (int) $this->getRequest()->getParam('id');

            if (!$quoteItemId) {
                throw new \Magento\Core\Exception(__('Quote item id is not received.'));
            }

            $quoteItem = $this->_objectManager->create('Magento\Sales\Model\Quote\Item')->load($quoteItemId);
            if (!$quoteItem->getId()) {
                throw new \Magento\Core\Exception(__('Quote item is not loaded.'));
            }

            $configureResult->setOk(true);
            $optionCollection = $this->_objectManager->create('Magento\Sales\Model\Quote\Item\Option')->getCollection()
                    ->addItemFilter(array($quoteItemId));
            $quoteItem->setOptions($optionCollection->getOptionsByItem($quoteItem));

            $configureResult->setBuyRequest($quoteItem->getBuyRequest());
            $configureResult->setCurrentStoreId($quoteItem->getStoreId());
            $configureResult->setProductId($quoteItem->getProductId());
            $sessionQuote = $this->_objectManager->get('Magento\Backend\Model\Session\Quote');
            $configureResult->setCurrentCustomerId($sessionQuote->getCustomerId());
        } catch (\Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        // Render page
        /* @var $helper \Magento\Catalog\Helper\Product\Composite */
        $helper = $this->_objectManager->get('Magento\Catalog\Helper\Product\Composite');
        $helper->renderConfigureResult($configureResult);
    }

    /**
     * Reload quote
     *
     * @return \Magento\AdvancedCheckout\Controller\Adminhtml\Index
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
        } catch (\Magento\AdvancedCheckout\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $criticalException = true;
        } catch (\Magento\Core\Exception $e) {
            $this->_reloadQuote();
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_reloadQuote();
            $this->messageManager->addException($e, $e->getMessage());
        }

        $asJson = $this->getRequest()->getParam('json');
        $block = $this->getRequest()->getParam('block');

        $update = $this->_view->getLayout()->getUpdate();
        if ($asJson) {
            $update->addHandle('checkout_index_manage_load_block_json');
        } else {
            $update->addHandle('checkout_index_manage_load_block_plain');
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
                $update->addHandle('checkout_index_manage_load_block_' . $block);
            }
        }

        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();
        $result = $this->_view->getLayout()->renderElement('content');
        if ($this->getRequest()->getParam('as_js_varname')) {
            $this->_objectManager->get('Magento\Backend\Model\Session')->setUpdateResult($result);
            $this->_redirect('checkout/*/showUpdateResult');
        } else {
            $this->getResponse()->setBody($result);
        }
    }

    /**
     * Returns item info by list and list item id
     * Returns object on success or false on error. Returned object has following keys:
     *  - product_id - null if no item found
     *  - buy_request - \Magento\Object, empty if not buy request stored for this item
     *
     * @param string $listType
     * @param int    $itemId
     *
     * @return \Magento\Object|false
     */
    protected function _getListItemInfo($listType, $itemId)
    {
        $productId = null;
        $buyRequest = new \Magento\Object();
        switch ($listType) {
            case 'wishlist':
                $item = $this->_objectManager->create('Magento\Wishlist\Model\Item')
                    ->loadWithOptions($itemId, 'info_buyRequest');
                if ($item->getId()) {
                    $productId = $item->getProductId();
                    $buyRequest = $item->getBuyRequest();
                }
                break;
            case 'ordered':
                $item = $this->_objectManager->create('Magento\Sales\Model\Order\Item')
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

        return new \Magento\Object(array('product_id' => $productId, 'buy_request' => $buyRequest));
    }

    /**
     * Wrapper for _getListItemInfo() - extends with additional list types. New method has been created to leave
     * definition of original method unchanged (add_by_sku list type utilizes additional parameter - $info).
     * @see _getListItemInfo() for return format
     *
     * @param string $listType
     * @param int    $itemId
     * @param array  $info
     * @return \Magento\Object|false
     */
    protected function _getInfoForListItem($listType, $itemId, $info)
    {
        $productId = null;
        $buyRequest = new \Magento\Object();
        switch ($listType) {
            case \Magento\AdvancedCheckout\Block\Adminhtml\Sku\AbstractSku::LIST_TYPE:
                $info['sku'] = $itemId;
            // fall-through is intentional
            case \Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\AbstractErrors::LIST_TYPE:
                if ((!isset($info['sku'])) || (string)$info['sku'] == '') { // Allow SKU == '0'
                    return false;
                }
                $item = $this->getCartModel()->prepareAddProductBySku($info['sku'], $info['qty'], $info);
                if ($item['code'] != \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS) {
                    return false;
                }
                $productId = $item['item']['id'];
                break;

            default:
                return $this->_getListItemInfo($listType, $itemId);
        }
        return new \Magento\Object(array('product_id' => $productId, 'buy_request' => $buyRequest));
    }

    /**
     * Processing request data
     *
     * @return \Magento\AdvancedCheckout\Controller\Adminhtml\Index
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
                        /* @var $error \Magento\Message\Error */
                        $this->messageManager->addError($error->getText());
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
                \Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\AbstractErrors::LIST_TYPE,
                \Magento\AdvancedCheckout\Block\Adminhtml\Sku\AbstractSku::LIST_TYPE,
            );
            /* @var $productHelper \Magento\Catalog\Helper\Product */
            $productHelper = $this->_objectManager->get('Magento\Catalog\Helper\Product');
            $listTypes = array_filter(explode(',', $listTypes));
            if (in_array(\Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\AbstractErrors::LIST_TYPE, $listTypes)) {
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
                        } catch (\Magento\Core\Exception $e){
                            $this->messageManager->addError($e->getMessage());
                        } catch (\Exception $e){
                            $this->_objectManager->get('Magento\Logger')->logException($e);
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
        /* @var $productHelper \Magento\Catalog\Helper\Product */
        $productHelper = $this->_objectManager->get('Magento\Catalog\Helper\Product');
        foreach ($items as $id => $item) {
            $buyRequest = new \Magento\Object($item);
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
        $session = $this->_objectManager->get('Magento\Backend\Model\Session');
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
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->_redirect('customer/index');
            $this->_redirectFlag = true;
        }
        if ($this->_redirectFlag) {
            return;
        }

        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_objectManager->get('Magento\AdvancedCheckout\Helper\Data');
        $rows = $helper->isSkuFileUploaded($this->getRequest())
            ? $helper->processSkuFileUploading()
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

        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }
}
