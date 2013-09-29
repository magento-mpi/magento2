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
 * Admin Checkout processing model
 *
 * @method bool hasErrorMessage()
 * @method string getErrorMessage()
 * @method setErrorMessage(string $message)
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
namespace Magento\AdvancedCheckout\Model;

class Cart extends \Magento\Object implements \Magento\Checkout\Model\Cart\CartInterface
{
    /**
     * Context of the cart - admin order
     */
    const CONTEXT_ADMIN_ORDER = 'admin_order';
    /**
     * Context of the cart - admin checkout
     */
    const CONTEXT_ADMIN_CHECKOUT = 'admin_checkout';
    /**
     * Context of the cart - frontend
     */
    const CONTEXT_FRONTEND = 'frontend';

    /**
     * Context of the cart
     *
     * @var string
     */
    protected $_context;

    /**
     * Quote instance
     *
     * @var \Magento\Sales\Model\Quote|null
     */
    protected $_quote;

    /**
     * Customer model instance
     *
     * @var \Magento\Customer\Model\Customer|null
     */
    protected $_customer;

    /**
     * List of result errors
     *
     * @var array
     */
    protected $_resultErrors = array();

    /**
     * List of currently affected items skus
     *
     * @var array
     */
    protected $_currentlyAffectedItems = array();

    /**
     * Configs of currently affected items
     *
     * @var array
     */
    protected $_affectedItemsConfig = array();

    /**
     * Cart instance
     *
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * Product options for configuring
     *
     * @var array
     */
    protected $_successOptions = array();

    /**
     * Instance of current store
     *
     * @var null|\Magento\Core\Model\Store
     */
    protected $_currentStore = null;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData = null;

    /**
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $_checkoutData = null;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Core\Model\Message
     */
    protected $_coreMessage;

    /**
     * @var \Magento\Adminhtml\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * Sales quote factory
     *
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * Catalog product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Wishlist factory
     *
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * Catalog inventory stock item factory
     *
     * @var \Magento\CatalogInventory\Model\Stock\ItemFactory
     */
    protected $_stockItemFactory;

    /**
     * Catalog product option factory
     *
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $_optionFactory;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Core\Model\Message $coreMessage
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Magento\CatalogInventory\Model\Stock\ItemFactory $stockItemFactory
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Core\Model\Message $coreMessage,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\AdvancedCheckout\Helper\Data $checkoutData,
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Magento\CatalogInventory\Model\Stock\ItemFactory $stockItemFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\LocaleInterface $locale,
        array $data = array()
    ) {
        $this->_cart = $cart;
        $this->_sessionQuote = $sessionQuote;
        $this->_coreMessage = $coreMessage;
        $this->_eventManager = $eventManager;
        $this->_checkoutData = $checkoutData;
        $this->_customerData = $customerData;
        $this->_optionFactory = $optionFactory;
        $this->_stockItemFactory = $stockItemFactory;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_productFactory = $productFactory;
        $this->_quoteFactory = $quoteFactory;
        $this->_storeManager = $storeManager;
        $this->_locale = $locale;
    }

    /**
     * Set context of the cart
     *
     * @param string $context
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function setContext($context)
    {
        $this->_context = $context;
        return $this;
    }

    /**
     * Setter for $_customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function setCustomer($customer)
    {
        if ($customer instanceof \Magento\Object && $customer->getId()) {
            $this->_customer = $customer;
            $this->_quote = null;
        }
        return $this;
    }

    /**
     * Getter for $_customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->_customer;
    }

    /**
     * Return quote store
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        return $this->getQuote()->getStore();
    }

    /**
     * Return current active quote for specified customer
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        if (!is_null($this->_quote)) {
            return $this->_quote;
        }

        $this->_quote = $this->_quoteFactory->create();

        if ($this->getCustomer() !== null) {
            $this->_quote
                ->setSharedStoreIds($this->getQuoteSharedStoreIds())
                ->loadByCustomer($this->getCustomer()->getId());
        }

        return $this->_quote;
    }

    /**
     * Sets different quote model
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function setQuote(\Magento\Sales\Model\Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Return quote instance depending on current area
     *
     * @return \Magento\Adminhtml\Model\Session\Quote|\Magento\Sales\Model\Quote
     */
    public function getActualQuote()
    {
        if ($this->_storeManager->getStore()->isAdmin()) {
            return $this->_quote->getQuote();
        } else {
            if (!$this->getCustomer()) {
                $customer = $this->_customerData->getCustomer();
                if ($customer) {
                    $this->setCustomer($customer);
                }
            }
            return $this->getQuote();
        }
    }

    /**
     * Return appropriate store ids for retrieving quote in current store
     * Correct customer shared store ids when customer has Admin Store
     *
     * @return array
     */
    public function getQuoteSharedStoreIds()
    {
        if ($this->getStoreId()) {
            return $this->_storeManager->getStore($this->getStoreId())
                ->getWebsite()
                ->getStoreIds();
        }
        if (!$this->getCustomer()) {
            return array();
        }
        if ((bool)$this->getCustomer()->getSharingConfig()->isWebsiteScope()) {
            return $this->_storeManager->getWebsite($this->getCustomer()->getWebsiteId())->getStoreIds();
        } else {
            return $this->getCustomer()->getSharedStoreIds();
        }
    }

    /**
     * Create quote by demand or return active customer quote if it exists
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function createQuote()
    {
        if (!$this->getQuote()->getId() && $this->getCustomer() !== null) {
            $this->getQuote()
                ->assignCustomer($this->getCustomer())
                ->save();
        }
        return $this->getQuote();
    }

    /**
     * Recollect quote and save it
     *
     * @param bool $recollect Collect quote totals or not
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function saveQuote($recollect = true)
    {
        if (!$this->getQuote()->getId()) {
            return $this;
        }
        if ($recollect) {
            $this->getQuote()->collectTotals();
        }
        $this->getQuote()->save();
        return $this;
    }

    /**
     * Return preferred non-admin store Id
     * If Customer has active quote - return its store, otherwise try to get customer store or default store
     *
     * @return int|bool
     */
    public function getPreferredStoreId()
    {
        $quote = $this->getQuote();
        $customer = $this->getCustomer();

        if ($quote->getId() && $quote->getStoreId()) {
            $storeId = $quote->getStoreId();
        } elseif ($customer !== null && $customer->getStoreId() && !$customer->getStore()->isAdmin()) {
            $storeId = $customer->getStoreId();
        } else {
            $customerStoreIds = $this->getQuoteSharedStoreIds(); //$customer->getSharedStoreIds();
            $storeId = array_shift($customerStoreIds);
            if ($this->_storeManager->getStore($storeId)->isAdmin()) {
                $defaultStore = $this->_storeManager->getAnyStoreView();
                if ($defaultStore) {
                    $storeId = $defaultStore->getId();
                }
            }
        }

        return $storeId;
    }

    /**
     * Add product to current order quote
     *
     * $config can be integer qty (older behaviour, when no product configuration was possible)
     * or it can be array of options (newer behaviour).
     *
     * In case of older behaviour same product ids are not added, but quote item qty is increased.
     * In case of newer behaviour same product ids with different configs are added as separate quote items.
     *
     * @param   mixed $product
     * @param   array|float|int|\Magento\Object $config
     * @throws  \Magento\Core\Exception
     * @return  \Magento\Adminhtml\Model\Sales\Order\Create
     */
    public function addProduct($product, $config = 1)
    {
        if (is_array($config) || ($config instanceof \Magento\Object)) {
            $config = is_array($config) ? new \Magento\Object($config) : $config;
            $qty = (float) $config->getQty();
            $separateSameProducts = true;
        } else {
            $qty = (float) $config;
            $config = new \Magento\Object();
            $config->setQty($qty);
            $separateSameProducts = false;
        }

        if (!($product instanceof \Magento\Catalog\Model\Product)) {
            $productId = $product;
            $product = $this->_productFactory->create()
                ->setStore($this->getStore())
                ->setStoreId($this->getStore()->getId())
                ->load($product);
            if (!$product->getId()) {
                throw new \Magento\Core\Exception(
                    __('Failed to add a product to cart by id "%1".', $productId)
                );
            }
        }

        if ($product->getStockItem()) {
            if (!$product->getStockItem()->getIsQtyDecimal()) {
                $qty = (int)$qty;
            } else {
                $product->setIsQtyDecimal(1);
            }
        }
        $qty = $qty > 0 ? $qty : 1;

        $item = null;
        if (!$separateSameProducts) {
            $item = $this->getQuote()->getItemByProduct($product);
        }
        if ($item) {
            $item->setQty($item->getQty() + $qty);
        } else {
            $item = $this->getQuote()->addProduct($product, $config);
            if (is_string($item)) {
                throw new \Magento\Core\Exception($item);
            }
            $item->checkData();
        }

        $this->setRecollect(true);
        return $this;
    }

    /**
     * Add new item to quote based on existing order Item
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param int|float $qty
     * @return \Magento\Sales\Model\Quote\Item
     * @throws \Magento\Core\Exception
     */
    public function reorderItem(\Magento\Sales\Model\Order\Item $orderItem, $qty = 1)
    {
        if (!$orderItem->getId()) {
            throw new \Magento\Core\Exception(__('Something went wrong reordering this product.'));
        }

        $product = $this->_productFactory->create()
            ->setStoreId($this->getStore()->getId())
            ->load($orderItem->getProductId());

        if ($product->getId()) {
            $info = $orderItem->getProductOptionByCode('info_buyRequest');
            $info = new \Magento\Object($info);
            $product->setSkipCheckRequiredOption(true);
            $item = $this->createQuote()->addProduct($product, $info);
            if (is_string($item)) {
                throw new \Magento\Core\Exception($item);
            }

            $item->setQty($qty);

            if ($additionalOptions = $orderItem->getProductOptionByCode('additional_options')) {
                $item->addOption(new \Magento\Object(
                    array(
                        'product'   => $item->getProduct(),
                        'code'      => 'additional_options',
                        'value'     => serialize($additionalOptions)
                    )
                ));
            }

            $this->_eventManager->dispatch('sales_convert_order_item_to_quote_item', array(
                'order_item' => $orderItem,
                'quote_item' => $item,
            ));

            return $item;

        } else {
            throw new \Magento\Core\Exception(__('Something went wrong reordering this product.'));
        }
    }

    /**
     * Adds error of operation either to internal array or directly to session (if set)
     *
     * @param string $message
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    protected function _addResultError($message)
    {
        $session = $this->getSession();
        if ($session) {
            $session->addError($message);
        } else {
            $this->_resultErrors[] = $message;
        }
        return $this;
    }

    /**
     * Returns array of errors encountered during previous operations
     *
     * @return array
     */
    protected function getResultErrors()
    {
        return $this->_resultErrors;
    }

    /**
     * Clears array of operation errors, so caller will get only errors related to last operation
     *
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    protected function clearResultErrors()
    {
        $this->_resultErrors = array();
        return $this;
    }

    /**
     * Add multiple products to current order quote.
     * Errors can be received via getResultErrors() or directly into session if it was set via setSession().
     *
     * @param   array $products
     * @return  \Magento\AdvancedCheckout\Model\Cart|Exception
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $config) {
            $config['qty'] = isset($config['qty']) ? (float)$config['qty'] : 1;
            try {
                $this->addProduct($productId, $config);
            } catch (\Magento\Core\Exception $e) {
                $this->_addResultError($e->getMessage());
            } catch (\Exception $e) {
                return $e;
            }
        }

        return $this;
    }

    /**
     * Remove items from quote or move them to wishlist etc.
     *
     * @param array $data Array of items
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function updateQuoteItems($data)
    {
        if (!$this->getQuote()->getId() || !is_array($data)) {
            return $this;
        }

        foreach ($data as $itemId => $info) {
            if (!empty($info['configured'])) {
                $item = $this->getQuote()->updateItem($itemId, new \Magento\Object($info));
                $itemQty = (float) $item->getQty();
            } else {
                $item = $this->getQuote()->getItemById($itemId);
                $itemQty = (float) $info['qty'];
            }

            if ($item && $item->getProduct()->getStockItem()) {
                if (!$item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                    $itemQty = (int) $itemQty;
                } else {
                    $item->setIsQtyDecimal(1);
                }
            }

            $itemQty = ($itemQty > 0) ? $itemQty : 1;
            if (isset($info['custom_price'])) {
                $itemPrice = $this->_parseCustomPrice($info['custom_price']);
            } else {
                $itemPrice = null;
            }
            $noDiscount = !isset($info['use_discount']);

            if (empty($info['action']) || !empty($info['configured'])) {
                if ($item) {
                    $item->setQty($itemQty);
                    $item->setCustomPrice($itemPrice);
                    $item->setOriginalCustomPrice($itemPrice);
                    $item->setNoDiscount($noDiscount);
                    $item->getProduct()->setIsSuperMode(true);
                    $item->checkData();
                }
            } else {
                $this->moveQuoteItem($item->getId(), $info['action'], $itemQty);
            }
        }
        if ($this->_needCollectCart === true) {
            $this->getCustomerCart()
                ->collectTotals()
                ->save();
        }
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Move quote item to wishlist.
     * Errors can be received via getResultErrors() or directly into session if it was set via setSession().
     *
     * @param \Magento\Sales\Model\Quote\Item|int $item
     * @param string $moveTo Destination storage
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function moveQuoteItem($item, $moveTo)
    {
        $item = $this->_getQuoteItem($item);
        if ($item) {
            $moveTo = explode('_', $moveTo);
            if ($moveTo[0] == 'wishlist') {
                $wishlist = null;
                if (!isset($moveTo[1])) {
                    $wishlist = $this->_wishlistFactory->create()->loadByCustomer($this->getCustomer(), true);
                } else {
                    $wishlist = $this->_wishlistFactory->create()->load($moveTo[1]);
                    if (!$wishlist->getId() || $wishlist->getCustomerId() != $this->getCustomer()->getId()) {
                        $wishlist = null;
                    }
                }
                if (!$wishlist) {
                    $this->_addResultError(__("We couldn't find this wish list."));
                    return $this;
                }
                $wishlist->setStore($this->getStore())
                    ->setSharedStoreIds($this->getStore()->getWebsite()->getStoreIds());
                if ($wishlist->getId() && $item->getProduct()->isVisibleInSiteVisibility()) {
                    $wishlistItem = $wishlist->addNewItem($item->getProduct(), $item->getBuyRequest());
                    if (is_string($wishlistItem)) {
                        $this->_addResultError($wishlistItem);
                    } else if ($wishlistItem->getId()) {
                        $this->getQuote()->removeItem($item->getId());
                    }
                }
            } else {
                $this->getQuote()->removeItem($item->getId());
            }
        }
        return $this;
    }

    /**
     * Create duplicate of quote preserving all data (items, addresses, payment etc.)
     *
     * @param \Magento\Sales\Model\Quote $quote Original Quote
     * @param bool $active Create active quote or not
     * @return \Magento\Sales\Model\Quote New created quote
     */
    public function copyQuote(\Magento\Sales\Model\Quote $quote, $active = false)
    {
        if (!$quote->getId()) {
            return $quote;
        }
        $newQuote = clone $quote;
        $newQuote->setId(null);
        $newQuote->setIsActive($active ? 1 : 0);
        $newQuote->save();

        // copy items with their options
        $newParentItemIds = array();
        foreach ($quote->getItemsCollection() as $item) {
            // save child items later
            if ($item->getParentItem()) {
                continue;
            }
            $oldItemId = $item->getId();
            $newItem = clone $item;
            $newItem->setQuote($newQuote);
            $newItem->save();
            $newParentItemIds[$oldItemId] = $newItem->getId();
        }

        // save children with new parent id
        foreach ($quote->getItemsCollection() as $item) {
            if (!$item->getParentItem() || !isset($newParentItemIds[$item->getParentItemId()])) {
                continue;
            }
            $newItem = clone $item;
            $newItem->setQuote($newQuote);
            $newItem->setParentItemId($newParentItemIds[$item->getParentItemId()]);
            $newItem->save();
        }

        // copy billing and shipping addresses
        foreach ($quote->getAddressesCollection() as $address) {
            $address->setQuote($newQuote);
            $address->setId(null);
            $address->save();
        }

        // copy payment info
        foreach ($quote->getPaymentsCollection() as $payment) {
            $payment->setQuote($newQuote);
            $payment->setId(null);
            $payment->save();
        }

        return $newQuote;
    }

    /**
     * Wrapper for getting quote item
     *
     * @param \Magento\Sales\Model\Quote\Item|int $item
     * @return \Magento\Sales\Model\Quote\Item|bool
     */
    protected function _getQuoteItem($item)
    {
        if ($item instanceof \Magento\Sales\Model\Quote\Item) {
            return $item;
        }
        elseif (is_numeric($item)) {
            return $this->getQuote()->getItemById($item);
        }
        return false;
    }

    /**
     * Add single item to stack and return extended pushed item. For return format see _addAffectedItem()
     *
     * @param string $sku
     * @param float  $qty
     * @param array  $config Configuration data of the product (if has been configured)
     * @return array
     */
    public function prepareAddProductBySku($sku, $qty, $config = array())
    {
        $affectedItems = $this->getAffectedItems();

        if (isset($affectedItems[$sku])) {
            /*
             * This condition made for case when user inputs same SKU in several rows. We need to update qty, otherwise
             * getQtyStatus() may return invalid result. If there's already such SKU in affected items array it means
             * that both came from add form (not from error grid as the case when there is several products with same
             * SKU requiring attention is not possible), so there could be no config.
             */
            if (empty($qty) || empty($affectedItems[$sku]['item']['qty'])) {
                $qty = '';
            } else {
                $qty += $affectedItems[$sku]['item']['qty'];
            }
            unset($affectedItems[$sku]);
            $this->setAffectedItems($affectedItems);
        }

        $checkedItem = $this->checkItem($sku, $qty, $config);
        $code = $checkedItem['code'];
        unset($checkedItem['code']);
        return $this->_addAffectedItem($checkedItem, $code);
    }

    /**
     * Check submitted SKUs
     *
     * @see saveAffectedProducts()
     * @param array $items Example: [['sku' => 'simple1', 'qty' => 2], ['sku' => 'simple2', 'qty' => 3], ...]
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function prepareAddProductsBySku(array $items)
    {
        foreach ($items as $item) {
            $item += array('sku' => '', 'qty' => '');
            $item = $this->_getValidatedItem($item['sku'], $item['qty']);

            if ($item['code'] != \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_EMPTY) {
                $this->prepareAddProductBySku($item['sku'], $item['qty']);
            }
        }
        return $this;
    }

    /**
     * Checks whether requested quantity is allowed taking into account that some amount already added to quote.
     * Returns TRUE if everything is okay
     * Returns array in below format on error:
     * [
     *  'status' => string (see \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_* constants),
     *  'qty_max_allowed' => int (optional, if 'status'==ADD_ITEM_STATUS_FAILED_QTY_ALLOWED)
     * ]
     *
     * @param \Magento\CatalogInventory\Model\Stock\Item $stockItem
     * @param \Magento\Catalog\Model\Product             $product
     * @param float                                  $requestedQty
     * @return array|true
     */
    public function getQtyStatus(
        \Magento\CatalogInventory\Model\Stock\Item $stockItem,
        \Magento\Catalog\Model\Product $product,
        $requestedQty
    ) {
        $result = $stockItem->checkQuoteItemQty($requestedQty, $requestedQty);
        if ($result->getHasError()) {
            $return = array();

            switch ($result->getErrorCode()) {
                case 'qty_increments':
                    $status = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INCREMENTS;
                    $return['qty_increments'] = $stockItem->getQtyIncrements();
                    break;
                case 'qty_min':
                    $status = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART;
                    $return['qty_min_allowed'] = $stockItem->getMinSaleQty();
                    break;
                case 'qty_max':
                    $status = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART;
                    $return['qty_max_allowed'] = $stockItem->getMaxSaleQty();
                    break;
                default:
                    $status = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED;
                    $return['qty_max_allowed'] = $stockItem->getStockQty();
            }

            $return['status'] = $status;
            $return['error'] = $result->getMessage();

            return $return;
        }
        return true;
    }

    /**
     * Decide whether product has been configured or not
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array                      $config
     * @return bool
     */
    protected function _isConfigured(\Magento\Catalog\Model\Product $product, $config)
    {
        // If below POST fields were submitted - this is product's options, it has been already configured
        switch ($product->getTypeId()) {
            case \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE:
            case \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL:
                return isset($config['options']);
            case \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE:
                return isset($config['super_attribute']);
            case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
                return isset($config['bundle_option']);
            case \Magento\Catalog\Model\Product\Type::TYPE_GROUPED:
                return isset($config['super_group']);
            case \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD:
                return isset($config['giftcard_amount']);
            case \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE:
                return isset($config['links']);
        }
        return false;
    }

    /**
     * Load product by specified sku
     *
     * @param string $sku
     * @return bool|\Magento\Catalog\Model\Product
     */
    protected function _loadProductBySku($sku)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_productFactory->create()
            ->setStore($this->getCurrentStore())
            ->loadByAttribute('sku', $sku);
        if ($product && $product->getId()) {
            $this->_stockItemFactory->create()->assignProduct($product);
        }

        return $product;
    }

    /**
     * Check whether required option is not missed, add values to configuration
     *
     * @param array $skuParts
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return bool
     */
    protected function _processProductOption(array &$skuParts, \Magento\Catalog\Model\Product\Option $option)
    {
        $missedRequired = true;
        $optionValues = $option->getValues();
        if (empty($optionValues)) {
            if ($option->hasSku()) {
                $found = array_search($option->getSku(), $skuParts);
                if ($found !== false) {
                    unset($skuParts[$found]);
                }
            }
            // we are not able to configure such option automatically
            return !$missedRequired;
        }

        foreach ($optionValues as $optionValue) {
            $found = array_search($optionValue->getSku(), $skuParts);
            if ($found !== false) {
                $this->_addSuccessOption($option, $optionValue);
                unset($skuParts[$found]);
                // we've found the value of required option
                $missedRequired = false;
                if (!$this->_isOptionMultiple($option)) {
                    break 1;
                }
            }
        }

        return !$missedRequired;
    }

    /**
     * Load product with its options by specified sku
     *
     * @param string $sku
     * @param array $config
     * @return bool|\Magento\Catalog\Model\Product
     */
    protected function _loadProductWithOptionsBySku($sku, $config = array())
    {
        $product = $this->_loadProductBySku($sku);
        if ($product && $product->getId()) {
            return $product;
        }

        $skuParts = explode('-', $sku);
        $primarySku = array_shift($skuParts);

        if (empty($primarySku) || $primarySku == $sku) {
            return false;
        }

        $product = $this->_loadProductBySku($primarySku);

        if ($product && $this->_shouldBeConfigured($product) && $this->_isConfigured($product, $config)) {
            return $product;
        }

        if ($product && $product->getId()) {
            $missedRequiredOption = false;
            $this->_successOptions = array();

            /** @var $option \Magento\Catalog\Model\Product\Option */
            $option = $this->_optionFactory->create()
                ->setAddRequiredFilter(true)
                ->setAddRequiredFilterValue(true);

            foreach ($option->getProductOptionCollection($product) as $requiredOption) {
                $missedRequiredOption = !$this->_processProductOption($skuParts, $requiredOption)
                    || $missedRequiredOption;
            }

            $option->setAddRequiredFilterValue(false);
            foreach ($option->getProductOptionCollection($product) as $productOption) {
                $this->_processProductOption($skuParts, $productOption);
            }

            if (!empty($skuParts)) {
                return false;
            }

            if (!$missedRequiredOption && !empty($this->_successOptions)) {
                $product->setConfiguredOptions($this->_successOptions);
                $this->setAffectedItemConfig($sku, array('options' => $this->_successOptions));
                $this->_successOptions = array();
            }
        }

        return $product;
    }

    /**
     * Check whether specified option could have multiple values
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return bool
     */
    protected function _isOptionMultiple($option)
    {
        switch ($option->getType()) {
            case \Magento\Catalog\Model\Product\Option::OPTION_TYPE_MULTIPLE:
            case \Magento\Catalog\Model\Product\Option::OPTION_TYPE_CHECKBOX:
                return true;
        }
        return false;
    }

    /**
     * Add product option for configuring
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @param \Magento\Catalog\Model\Product\Option\Value $value
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    protected function _addSuccessOption($option, $value)
    {
        if ($this->_isOptionMultiple($option)) {
            if (isset($this->_successOptions[$option->getOptionId()])
                && is_array($this->_successOptions[$option->getOptionId()])
            ) {
                $this->_successOptions[$option->getOptionId()][] = $value->getOptionTypeId();
            } else {
                $this->_successOptions[$option->getOptionId()] = array($value->getOptionTypeId());
            }
        } else {
            $this->_successOptions[$option->getOptionId()] = $value->getOptionTypeId();
        }

        return $this;
    }

    /**
     * Check whether current context is checkout
     *
     * @return bool
     */
    protected function _isCheckout()
    {
        return in_array($this->_context, array(self::CONTEXT_FRONTEND, self::CONTEXT_ADMIN_CHECKOUT));
    }

    /**
     * Check whether current context is frontend
     *
     * @return bool
     */
    protected function _isFrontend()
    {
        return $this->_context == self::CONTEXT_FRONTEND;
    }

    /**
     * Update item with assigning the code to it
     *
     * @param array $item
     * @param string $code
     * @return array
     */
    protected function _updateItem($item, $code)
    {
        $item['code'] = $code;
        return $item;
    }

    /**
     * Check item before adding by SKU
     *
     * @param string $sku
     * @param float  $qty
     * @param array  $config Configuration data of the product (if has been configured)
     * @return array
     */
    public function checkItem($sku, $qty, $config = array())
    {
        $item = $this->_getValidatedItem($sku, $qty);
        if ($item['code'] == \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_EMPTY) {
            return $item;
        }
        $prevalidateStatus = $item['code'];
        unset($item['code']);

        if (!empty($config)) {
            $this->setAffectedItemConfig($sku, $config);
        }

        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_loadProductWithOptionsBySku($item['sku'], $config);

        if ($product && $product->hasConfiguredOptions()) {
            $config['options'] = $product->getConfiguredOptions();
        }

        if ($product && $product->getId()) {
            $item['id'] = $product->getId();

            $item['is_qty_disabled'] = $product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_GROUPED;

            if ($this->_isCheckout() && $product->isDisabled()) {
                $item['is_configure_disabled'] = true;
                $failCode = $this->_context == self::CONTEXT_FRONTEND
                    ? \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU
                    : \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_DISABLED;
                return $this->_updateItem($item, $failCode);
            }

            if ($this->_isFrontend() && true === $product->getDisableAddToCart()) {
                return $this->_updateItem($item, \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_PERMISSIONS);
            }

            $productWebsiteValidationResult = $this->_validateProductWebsite($product);
            if ($productWebsiteValidationResult !== true) {
                $item['is_configure_disabled'] = true;
                return $this->_updateItem($item, $productWebsiteValidationResult);
            }

            if ($this->_isCheckout() && $this->_isProductOutOfStock($product)) {
                $item['is_configure_disabled'] = true;
                return $this->_updateItem($item, \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK);
            }

            if ($this->_shouldBeConfigured($product)) {
                if (!$this->_isConfigured($product, $config)) {
                    $failCode = (!$this->_isFrontend() || $product->isVisibleInSiteVisibility())
                        ? \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_CONFIGURE
                        : \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU;
                    return $this->_updateItem($item, $failCode);
                } else {
                    $item['code'] = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS;
                }
            }

            if ($prevalidateStatus != \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS) {
                return $this->_updateItem($item, $prevalidateStatus);
            }

            if ($this->_isFrontend() && !$item['is_qty_disabled']) {
                $qtyStatus = $this->getQtyStatus($product->getStockItem(), $product, $item['qty']);
                if ($qtyStatus === true) {
                    return $this->_updateItem($item, \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS);
                } else {
                    $item['code'] = $qtyStatus['status'];
                    unset($qtyStatus['status']);
                    // Add qty_max_allowed and qty_min_allowed, if present
                    $item = array_merge($item, $qtyStatus);
                    return $item;
                }
            }
        } else {
            return $this->_updateItem($item, \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU);
        }

        return $this->_updateItem($item, \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS);
    }

    /**
     * Check product availability for current website
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool|string
     */
    protected function _validateProductWebsite($product)
    {
        if (in_array($this->getCurrentStore()->getWebsiteId(), $product->getWebsiteIds())) {
            return true;
        }

        return ($this->_storeManager->getStore()->isAdmin())
            ? \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_WEBSITE
            : \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU;
    }

    /**
     * Returns validated item
     *
     * @param $sku string|array
     * @param $qty string|int|float
     *
     * @return array
     */
    protected function _getValidatedItem($sku, $qty)
    {
        $code = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS;
        if ($sku == '') {
            $code = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_EMPTY;
        } else {
            if (!\Zend_Validate::is($qty, 'Float')) {
                $code = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_NUMBER;
            } else {
                $qty = $this->_locale->getNumber($qty);
                if ($qty <= 0) {
                    $code = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_NON_POSITIVE;
                } elseif ($qty < 0.0001 || $qty > 99999999.9999) {
                    // same as app/design/frontend/enterprise/default/template/checkout/widget/sku.phtml
                    $code = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_RANGE;
                }
            }
        }

        if ($code != \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS) {
            $qty = '';
        }

        return array('sku' => $sku, 'qty' => $qty, 'code' => $code);
    }

    /**
     * Check whether specified product is out of stock
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    protected function _isProductOutOfStock($product)
    {
        if ($product->isComposite()) {
            if (!$product->getStockItem()->getIsInStock()) {
                return true;
            }
            $productsByGroups = $product->getTypeInstance()->getProductsToPurchaseByReqGroups($product);
            foreach ($productsByGroups as $productsInGroup) {
                foreach ($productsInGroup as $childProduct) {
                    if (($childProduct->hasStockItem() && $childProduct->getStockItem()->getIsInStock())
                        && !$childProduct->isDisabled()
                    ) {
                        return false;
                    }
                }
            }
            return true;
        }

        /** @var $stockItem \Magento\CatalogInventory\Model\Stock\Item */
        $stockItem = $this->_stockItemFactory->create();
        $stockItem->loadByProduct($product);
        $stockItem->setProduct($product);
        return !$stockItem->getIsInStock();
    }

    /**
     * Check whether specified product should be configured
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    protected function _shouldBeConfigured($product)
    {
        if ($product->getTypeId() == \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE
            && !$product->getLinksPurchasedSeparately()
        ) {
            return false;
        }

        if ($product->isComposite() || $product->getRequiredOptions()) {
            return true;
        }

        switch ($product->getTypeId()) {
            case \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD:
            case \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE:
                return true;
        }

        return false;
    }

    /**
     * Set config for specific item
     *
     * @param string $sku
     * @param array  $config
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function setAffectedItemConfig($sku, $config)
    {
        if (!empty($sku) && !empty($config) && is_array($config)) {
            $this->_affectedItemsConfig[$sku] = $config;
        }
        return $this;
    }

    /**
     * Return config of specific item
     *
     * @param string $sku
     * @return array
     */
    public function getAffectedItemConfig($sku)
    {
        return isset($this->_affectedItemsConfig[$sku]) ? $this->_affectedItemsConfig[$sku] : array();
    }

    /**
     * Add products previously successfully processed by prepareAddProductsBySku() to cart
     *
     * @param \Magento\Checkout\Model\Cart\CartInterface|null $cart                 Custom cart model (different from
     *                                                                      checkout/cart)
     * @param bool                                    $saveQuote            Whether cart quote should be saved
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function saveAffectedProducts(\Magento\Checkout\Model\Cart\CartInterface $cart = null, $saveQuote = true)
    {
        $cart = $cart ? $cart : $this->_cart;
        $affectedItems = $this->getAffectedItems();
        foreach ($affectedItems as &$item) {
            if ($item['code'] == \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS) {
                $this->_safeAddProduct($item, $cart);
            }
        }
        $this->setAffectedItems($affectedItems);
        $this->removeSuccessItems();
        if ($saveQuote) {
            $cart->saveQuote();
        }
        return $this;
    }

    /**
     * Safely add product to cart, revert cart in error case
     *
     * @param array                              $item
     * @param \Magento\Checkout\Model\Cart\CartInterface $cart                 If we need to add product to different cart from
     *                                                                 checkout/cart
     * @param bool                               $suppressSuperMode
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    protected function _safeAddProduct(&$item, \Magento\Checkout\Model\Cart\CartInterface $cart, $suppressSuperMode = false)
    {
        $quote = $cart->getQuote();

        // copy data to temporary quote
        /** @var $temporaryQuote \Magento\Sales\Model\Quote */
        $temporaryQuote = $this->_quoteFactory->create();
        $temporaryQuote->setStore($quote->getStore())->setIsSuperMode($quote->getIsSuperMode());
        foreach ($quote->getAllItems() as $quoteItem) {
            $temporaryItem = clone $quoteItem;
            $temporaryItem->setQuote($temporaryQuote);
            $temporaryQuote->addItem($temporaryItem);
            $quoteItem->setClonnedItem($temporaryItem);

            //Check for parent item
            $parentItem = null;
            if ($quoteItem->getParentItem()) {
                $parentItem = $quoteItem->getParentItem();
                $temporaryItem->setParentProductId(null);
            } elseif ($quoteItem->getParentProductId()) {
                $parentItem = $quote->getItemById($quoteItem->getParentProductId());
            }
            if ($parentItem && $parentItem->getClonnedItem()) {
                $temporaryItem->setParentItem($parentItem->getClonnedItem());
            }
        }

        $cart->setQuote($temporaryQuote);
        $success = true;
        $skipCheckQty = !$suppressSuperMode && $this->_isCheckout() && !$this->_isFrontend()
            && empty($item['item']['is_qty_disabled']) && !$cart->getQuote()->getIsSuperMode();
        if ($skipCheckQty) {
            $cart->getQuote()->setIsSuperMode(true);
        }

        try {
            $config = $this->getAffectedItemConfig($item['item']['sku']);
            if (!empty($config)) {
                $config['qty'] = $item['item']['qty'];
            } else {
                // If second parameter of addProduct() is not an array than it is considered to be qty
                $config = $item['item']['qty'];
            }
            $cart->addProduct($item['item']['id'], $config);
        } catch (\Magento\Core\Exception $e) {
            if (!$suppressSuperMode) {
                $success = false;
                $item['code'] = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_UNKNOWN;
                if ($this->_isFrontend()) {
                    $item['item']['error'] = $e->getMessage();
                } else {
                    $item['error'] = $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $success = false;
            $item['code'] = \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_UNKNOWN;
            $error = __('The product cannot be added to cart.');
            if ($this->_isFrontend()) {
                $item['item']['error'] = $error;
            } else {
                $item['error'] = $error;
            }
        }
        if ($skipCheckQty) {
            $cart->getQuote()->setIsSuperMode(false);
            if ($success) {
                $cart->setQuote($quote);
                // we need add products with checking their stock qty
                return $this->_safeAddProduct($item, $cart, true);
            }
        }

        if ($success) {
            // copy temporary data to real quote
            $quote->removeAllItems();
            foreach ($temporaryQuote->getAllItems() as $quoteItem) {
                $quoteItem->setQuote($quote);
                $quote->addItem($quoteItem);
            }
        }

        $cart->setQuote($quote);

        return $this;
    }

    /**
     * Returns affected items
     * Return format:
     * sku(string) => [
     *  'item' => [
     *      'sku'             => string,
     *      'qty'             => int,
     *      'id'              => int (optional, if product does exist),
     *      'qty_max_allowed' => int (optional, if 'code'==ADD_ITEM_STATUS_FAILED_QTY_ALLOWED)
     *  ],
     *  'code' => string (see \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_*)
     * ]
     *
     * @see prepareAddProductsBySku()
     * @param null|int $storeId
     * @return array
     */
    public function getAffectedItems($storeId = null)
    {
        $storeId = (is_null($storeId)) ? $this->_storeManager->getStore()->getId() : (int)$storeId;
        $affectedItems = $this->_getHelper()->getSession()->getAffectedItems();

        return (isset($affectedItems[$storeId]) && is_array($affectedItems[$storeId]))
                ? $affectedItems[$storeId]
                : array();
    }

    /**
     * Returns only items with 'success' status
     *
     * @return array
     */
    public function getSuccessfulAffectedItems()
    {
        $items = array();
        foreach ($this->getAffectedItems() as $item) {
            if ($item['code'] == \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Set affected items
     *
     * @param array $items
     * @param null|int $storeId
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function setAffectedItems($items, $storeId = null)
    {
        $storeId = (is_null($storeId)) ? $this->_storeManager->getStore()->getId() : (int)$storeId;
        $affectedItems = $this->_getHelper()->getSession()->getAffectedItems();
        if (!is_array($affectedItems)) {
            $affectedItems = array();
        }

        $affectedItems[$storeId] = $items;
        $this->_getHelper()->getSession()->setAffectedItems($affectedItems);
        return $this;
    }

    /**
     * Retrieve info message
     *
     * @return array
     */
    public function getMessages()
    {
        $affectedItems = $this->getAffectedItems();
        $currentlyAffectedItemsCount  = count($this->_currentlyAffectedItems);
        $currentlyFailedItemsCount = 0;

        foreach ($this->_currentlyAffectedItems as $sku) {
            if (isset($affectedItems[$sku])
                && $affectedItems[$sku]['code'] != \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS
            ) {
                $currentlyFailedItemsCount++;
            }
        }

        $addedItemsCount = $currentlyAffectedItemsCount - $currentlyFailedItemsCount;

        $failedItemsCount = count($this->getFailedItems());
        $messages = array();
        if ($addedItemsCount) {
            $message = ($addedItemsCount == 1)
                    ? __('You added %1 product to your shopping cart.', $addedItemsCount)
                    : __('You added %1 products to your shopping cart.', $addedItemsCount);
            $messages[] = $this->_coreMessage->success($message);
        }
        if ($failedItemsCount) {
            $warning = ($failedItemsCount == 1)
                    ? __('%1 product requires your attention.', $failedItemsCount)
                    : __('%1 products require your attention.', $failedItemsCount);
            $messages[] = $this->_coreMessage->error($warning);
        }
        return $messages;
    }

    /**
     * Retrieve list of failed items. For return format see getAffectedItems().
     *
     * @return array
     */
    public function getFailedItems()
    {
        $failedItems = array();
        foreach ($this->getAffectedItems() as $item) {
            if ($item['code'] != \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS) {
                $failedItems[] = $item;
            }
        }
        return $failedItems;
    }

    /**
     * Add processed item to stack.
     * Return format:
     * [
     *  'item' => [
     *      'sku'             => string,
     *      'qty'             => int,
     *      'id'              => int (optional, if product does exist),
     *      'qty_max_allowed' => int (optional, if 'code'==ADD_ITEM_STATUS_FAILED_QTY_ALLOWED)
     *  ],
     *  'code' => string (see \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_*),
     *  'orig_qty' => string|int|float
     * ]
     *
     * @param array $item
     * @param string $code
     * @return array
     */
    protected function _addAffectedItem($item, $code)
    {
        if (!isset($item['sku']) || $code == \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_EMPTY) {
            return $this;
        }
        $sku = $item['sku'];
        $affectedItems = $this->getAffectedItems();
        $affectedItems[$sku] = array('item' => $item, 'code' => $code, 'orig_qty' => $item['qty']);
        $this->_currentlyAffectedItems[] = $sku;
        $this->setAffectedItems($affectedItems);
        return $affectedItems[$sku];
    }

    /**
     * Update qty of specified item
     *
     * @param string $sku
     * @param int $qty
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function updateItemQty($sku, $qty)
    {
        $affectedItems = $this->getAffectedItems();
        if (isset($affectedItems[$sku])) {
            $affectedItems[$sku]['item']['qty'] = $qty;
        }
        $this->setAffectedItems($affectedItems);
        return $this;
    }

    /**
     * Remove item from storage by specified key(sku)
     *
     * @param string $sku
     * @return bool
     */
    public function removeAffectedItem($sku)
    {
        $affectedItems = $this->getAffectedItems();
        if (isset($affectedItems[$sku])) {
            unset($affectedItems[$sku]);
            $this->setAffectedItems($affectedItems);
            return true;
        }
        return false;
    }

    /**
     * Remove all affected items from storage
     *
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function removeAllAffectedItems()
    {
        $this->setAffectedItems(array());
        return $this;
    }

    /**
     * Remove all affected items with code=success
     *
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function removeSuccessItems()
    {
        $affectedItems = $this->getAffectedItems();
        foreach ($affectedItems as $key => $item) {
            if ($item['code'] == \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS) {
                unset($affectedItems[$key]);
            }
        }
        $this->setAffectedItems($affectedItems);
        return $this;
    }

    /**
     * Retrieve helper instance
     *
     * @return \Magento\AdvancedCheckout\Helper\Data
     */
    protected function _getHelper()
    {
        return $this->_checkoutData;
    }

    /**
     * Sets session where data is going to be stored
     *
     * @param \Magento\Core\Model\Session\AbstractSession $session
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function setSession(\Magento\Core\Model\Session\AbstractSession $session)
    {
        $this->_getHelper()->setSession($session);
        return $this;
    }

    /**
     * Returns current session used to store data about affected items
     *
     * @return \Magento\Core\Model\Session\AbstractSession
     */
    public function getSession()
    {
        return $this->_getHelper()->getSession();
    }

    /**
     * Retrieve instance of current store
     *
     * @return \Magento\Core\Model\Store
     */
    public function getCurrentStore()
    {
        if (is_null($this->_currentStore)) {
            return $this->_storeManager->getStore();
        }
        return $this->_currentStore;
    }

    /**
     * Set current store
     *
     * @param mixed $store
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function setCurrentStore($store)
    {
        if (!is_null($store)) {
            $this->_currentStore = $this->_storeManager->getStore($store);
        }
        return $this;
    }
}
