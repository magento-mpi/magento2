<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order create model
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Model_Sales_Order_Create extends Magento_Object implements Magento_Checkout_Model_Cart_Interface
{
    /**
     * Quote session object
     *
     * @var Magento_Adminhtml_Model_Session_Quote
     */
    protected $_session;

    /**
     * Quote customer wishlist model object
     *
     * @var Magento_Wishlist_Model_Wishlist
     */
    protected $_wishlist;

    /**
     * Sales Quote instance
     *
     * @var Magento_Sales_Model_Quote
     */
    protected $_cart;

    /**
     * Catalog Compare List instance
     *
     * @var Magento_Catalog_Model_Product_Compare_List
     */
    protected $_compareList;

    /**
     * Re-collect quote flag
     *
     * @var boolean
     */
    protected $_needCollect;

    /**
     * Re-collect cart flag
     *
     * @var boolean
     */
    protected $_needCollectCart = false;

    /**
     * Collect (import) data and validate it flag
     *
     * @var boolean
     */
    protected $_isValidate              = false;

    /**
     * Customer instance
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Customer Address Form instance
     *
     * @var Magento_Customer_Model_Form
     */
    protected $_customerAddressForm;

    /**
     * Customer Form instance
     *
     * @var Magento_Customer_Model_Form
     */
    protected $_customerForm;

    /**
     * Array of validate errors
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Quote associated with the model
     *
     * @var Magento_Sales_Model_Quote
     */
    protected $_quote;

    public function __construct()
    {
        $this->_session = Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote');
    }

    /**
     * Set validate data in import data flag
     *
     * @param boolean $flag
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function setIsValidate($flag)
    {
        $this->_isValidate = (bool)$flag;
        return $this;
    }

    /**
     * Return is validate data in import flag
     *
     * @return boolean
     */
    public function getIsValidate()
    {
        return $this->_isValidate;
    }

    /**
     * Retrieve quote item
     *
     * @param   int|Magento_Sales_Model_Quote_Item $item
     * @return  Magento_Sales_Model_Quote_Item
     */
    protected function _getQuoteItem($item)
    {
        if ($item instanceof Magento_Sales_Model_Quote_Item) {
            return $item;
        } elseif (is_numeric($item)) {
            return $this->getSession()->getQuote()->getItemById($item);
        }
        return false;
    }

    /**
     * Initialize data for price rules
     *
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function initRuleData()
    {
        Mage::register('rule_data', new Magento_Object(array(
            'store_id'  => $this->_session->getStore()->getId(),
            'website_id'  => $this->_session->getStore()->getWebsiteId(),
            'customer_group_id' => $this->getCustomerGroupId(),
        )));
        return $this;
    }

    /**
     * Set collect totals flag for quote
     *
     * @param   bool $flag
     * @return  Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function setRecollect($flag)
    {
        $this->_needCollect = $flag;
        return $this;
    }

    /**
     * Recollect totals for customer cart.
     * Set recollect totals flag for quote
     *
     * @return  Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function recollectCart(){
        if ($this->_needCollectCart === true) {
            $this->getCustomerCart()
                ->collectTotals()
                ->save();
        }
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Quote saving
     *
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function saveQuote()
    {
        if (!$this->getQuote()->getId()) {
            return $this;
        }

        if ($this->_needCollect) {
            $this->getQuote()->collectTotals();
        }

        $this->getQuote()->save();
        return $this;
    }

    /**
     * Retrieve session model object of quote
     *
     * @return Magento_Adminhtml_Model_Session_Quote
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Retrieve quote object model
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->getSession()->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Set quote object
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function setQuote(Magento_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Initialize creation data from existing order
     *
     * @param Magento_Sales_Model_Order $order
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function initFromOrder(Magento_Sales_Model_Order $order)
    {
        $session = $this->getSession();
        $session->setData($order->getReordered() ? 'reordered' : 'order_id', $order->getId());
        $session->setCurrencyId($order->getOrderCurrencyCode());
        /* Check if we edit guest order */
        $session->setCustomerId($order->getCustomerId() ?: false);
        $session->setStoreId($order->getStoreId());

        /* Initialize catalog rule data with new session values */
        $this->initRuleData();
        foreach ($order->getItemsCollection(
            array_keys(Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray()),
            true
        ) as $orderItem) {
            /* @var $orderItem Magento_Sales_Model_Order_Item */
            if (!$orderItem->getParentItem()) {
                $qty = $orderItem->getQtyOrdered();
                if (!$order->getReordered()) {
                    $qty -= max($orderItem->getQtyShipped(), $orderItem->getQtyInvoiced());
                }

                if ($qty > 0) {
                    $item = $this->initFromOrderItem($orderItem, $qty);
                    if (is_string($item)) {
                        Mage::throwException($item);
                    }
                }
            }
        }

        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $addressDiff = array_diff_assoc($shippingAddress->getData(), $order->getBillingAddress()->getData());
            unset($addressDiff['address_type'], $addressDiff['entity_id']);
            $shippingAddress->setSameAsBilling(empty($addressDiff));
        }

        $this->_initBillingAddressFromOrder($order);
        $this->_initShippingAddressFromOrder($order);

        $quote = $this->getQuote();
        if (!$quote->isVirtual() && $this->getShippingAddress()->getSameAsBilling()) {
            $this->setShippingAsBilling(1);
        }

        $this->setShippingMethod($order->getShippingMethod());
        $quote->getShippingAddress()->setShippingDescription($order->getShippingDescription());

        $paymentData = $order->getPayment()->getData();
        if ('ccsave' !== $paymentData['method']) {
            unset($paymentData['cc_type'], $paymentData['cc_last4']);
            unset($paymentData['cc_exp_month'], $paymentData['cc_exp_year']);
        }
        $quote->getPayment()->addData($paymentData);

        $orderCouponCode = $order->getCouponCode();
        if ($orderCouponCode) {
            $quote->setCouponCode($orderCouponCode);
        }

        if ($quote->getCouponCode()) {
            $quote->collectTotals();
        }

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_copy_order', 'to_edit', $order, $quote);

        Mage::dispatchEvent('sales_convert_order_to_quote', array('order' => $order, 'quote' => $quote));

        if (!$order->getCustomerId()) {
            $quote->setCustomerIsGuest(true);
        }

        if ($session->getUseOldShippingMethod(true)) {
            /*
             * if we are making reorder or editing old order
             * we need to show old shipping as preselected
             * so for this we need to collect shipping rates
             */
            $this->collectShippingRates();
        } else {
            /*
             * if we are creating new order then we don't need to collect
             * shipping rates before customer hit appropriate button
             */
            $this->collectRates();
        }

        $quote->save();

        return $this;
    }

    protected function _initBillingAddressFromOrder(Magento_Sales_Model_Order $order)
    {
        $this->getQuote()->getBillingAddress()->setCustomerAddressId('');
        Mage::helper('Magento_Core_Helper_Data')->copyFieldset(
            'sales_copy_order_billing_address',
            'to_order',
            $order->getBillingAddress(),
            $this->getQuote()->getBillingAddress()
        );
    }

    protected function _initShippingAddressFromOrder(Magento_Sales_Model_Order $order)
    {
        $orderShippingAddress = $order->getShippingAddress();
        $quoteShippingAddress = $this->getQuote()->getShippingAddress()
            ->setCustomerAddressId('')
            ->setSameAsBilling($orderShippingAddress && $orderShippingAddress->getSameAsBilling());
        Mage::helper('Magento_Core_Helper_Data')->copyFieldset(
            'sales_copy_order_shipping_address',
            'to_order',
            $orderShippingAddress,
            $quoteShippingAddress
        );
    }

    /**
     * Initialize creation data from existing order Item
     *
     * @param Magento_Sales_Model_Order_Item $orderItem
     * @param int $qty
     * @return Magento_Sales_Model_Quote_Item | string
     */
    public function initFromOrderItem(Magento_Sales_Model_Order_Item $orderItem, $qty = null)
    {
        if (!$orderItem->getId()) {
            return $this;
        }

        $product = Mage::getModel('Magento_Catalog_Model_Product')
            ->setStoreId($this->getSession()->getStoreId())
            ->load($orderItem->getProductId());

        if ($product->getId()) {
            $product->setSkipCheckRequiredOption(true);
            $buyRequest = $orderItem->getBuyRequest();
            if (is_numeric($qty)) {
                $buyRequest->setQty($qty);
            }
            $item = $this->getQuote()->addProduct($product, $buyRequest);
            if (is_string($item)) {
                return $item;
            }

            if ($additionalOptions = $orderItem->getProductOptionByCode('additional_options')) {
                $item->addOption(new Magento_Object(
                    array(
                        'product' => $item->getProduct(),
                        'code' => 'additional_options',
                        'value' => serialize($additionalOptions)
                    )
                ));
            }

            Mage::dispatchEvent('sales_convert_order_item_to_quote_item', array(
                'order_item' => $orderItem,
                'quote_item' => $item
            ));
            return $item;
        }

        return $this;
    }

    /**
     * Retrieve customer wishlist model object
     *
     * @params bool $cacheReload pass cached wishlist object and get new one
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getCustomerWishlist($cacheReload = false)
    {
        if (!is_null($this->_wishlist) && !$cacheReload) {
            return $this->_wishlist;
        }

        if ($this->getSession()->getCustomer()->getId()) {
            $this->_wishlist = Mage::getModel('Magento_Wishlist_Model_Wishlist')->loadByCustomer(
                $this->getSession()->getCustomer(), true
            );
            $this->_wishlist->setStore($this->getSession()->getStore())
                ->setSharedStoreIds($this->getSession()->getStore()->getWebsite()->getStoreIds());
        } else {
            $this->_wishlist = false;
        }

        return $this->_wishlist;
    }

    /**
     * Retrieve customer cart quote object model
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getCustomerCart()
    {
        if (!is_null($this->_cart)) {
            return $this->_cart;
        }

        $this->_cart = Mage::getModel('Magento_Sales_Model_Quote');

        if ($this->getSession()->getCustomer()->getId()) {
            $this->_cart->setStore($this->getSession()->getStore())
                ->loadByCustomer($this->getSession()->getCustomer()->getId());
            if (!$this->_cart->getId()) {
                $this->_cart->assignCustomer($this->getSession()->getCustomer());
                $this->_cart->save();
            }
        }

        return $this->_cart;
    }

    /**
     * Retrieve customer compare list model object
     *
     * @return Magento_Catalog_Model_Product_Compare_List
     */
    public function getCustomerCompareList()
    {
        if (!is_null($this->_compareList)) {
            return $this->_compareList;
        }

        if ($this->getSession()->getCustomer()->getId()) {
            $this->_compareList = Mage::getModel('Magento_Catalog_Model_Product_Compare_List');
        } else {
            $this->_compareList = false;
        }
        return $this->_compareList;
    }

    public function getCustomerGroupId()
    {
        $groupId = $this->getQuote()->getCustomerGroupId();
        if (!$groupId) {
            $groupId = $this->getSession()->getCustomerGroupId();
        }
        return $groupId;
    }

    /**
     * Move quote item to another items list
     *
     * @param   int|Magento_Sales_Model_Quote_Item $item
     * @param   string $moveTo
     * @param   int $qty
     * @return  Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function moveQuoteItem($item, $moveTo, $qty)
    {
        $item = $this->_getQuoteItem($item);
        if ($item) {
            $removeItem = false;
            $moveTo = explode('_', $moveTo);
            switch ($moveTo[0]) {
                case 'order':
                    $info = $item->getBuyRequest();
                    $info->setOptions($this->_prepareOptionsForRequest($item))
                        ->setQty($qty);

                    $product = Mage::getModel('Magento_Catalog_Model_Product')
                        ->setStoreId($this->getQuote()->getStoreId())
                        ->load($item->getProduct()->getId());

                    $product->setSkipCheckRequiredOption(true);
                    $newItem = $this->getQuote()->addProduct($product, $info);

                    if (is_string($newItem)) {
                        Mage::throwException($newItem);
                    }
                    $product->unsSkipCheckRequiredOption();
                    $newItem->checkData();
                    $this->_needCollectCart = true;
                    break;
                case 'cart':
                    $cart = $this->getCustomerCart();
                    if ($cart && is_null($item->getOptionByCode('additional_options'))) {
                        //options and info buy request
                        $product = Mage::getModel('Magento_Catalog_Model_Product')
                            ->setStoreId($this->getQuote()->getStoreId())
                            ->load($item->getProduct()->getId());

                        $info = $item->getOptionByCode('info_buyRequest');
                        if ($info) {
                            $info = new Magento_Object(
                                unserialize($info->getValue())
                            );
                            $info->setQty($qty);
                            $info->setOptions($this->_prepareOptionsForRequest($item));
                        } else {
                            $info = new Magento_Object(array(
                                'product_id' => $product->getId(),
                                'qty' => $qty,
                                'options' => $this->_prepareOptionsForRequest($item)
                            ));
                        }

                        $cartItem = $cart->addProduct($product, $info);
                        if (is_string($cartItem)) {
                            Mage::throwException($cartItem);
                        }
                        $cartItem->setPrice($item->getProduct()->getPrice());
                        $this->_needCollectCart = true;
                        $removeItem = true;
                    }
                    break;
                case 'wishlist':
                    $wishlist = null;
                    if (!isset($moveTo[1])) {
                        $wishlist = Mage::getModel('Magento_Wishlist_Model_Wishlist')->loadByCustomer(
                            $this->getSession()->getCustomer(),
                            true
                        );
                    } else {
                        $wishlist = Mage::getModel('Magento_Wishlist_Model_Wishlist')->load($moveTo[1]);
                        if (!$wishlist->getId()
                            || $wishlist->getCustomerId() != $this->getSession()->getCustomerId()
                        ) {
                            $wishlist = null;
                        }
                    }
                    if (!$wishlist) {
                        Mage::throwException(Mage::helper('Magento_Wishlist_Helper_Data')->__('We couldn\'t find this wish list.'));
                    }
                    $wishlist->setStore($this->getSession()->getStore())
                        ->setSharedStoreIds($this->getSession()->getStore()->getWebsite()->getStoreIds());

                    if ($wishlist->getId() && $item->getProduct()->isVisibleInSiteVisibility()) {
                        $info = $item->getBuyRequest();
                        $info->setOptions($this->_prepareOptionsForRequest($item))
                            ->setQty($qty)
                            ->setStoreId($this->getSession()->getStoreId());
                        $wishlist->addNewItem($item->getProduct(), $info);
                        $removeItem = true;
                    }
                    break;
                case 'remove':
                    $removeItem = true;
                    break;
                default:
                    break;
            }
            if ($removeItem) {
                $this->getQuote()->deleteItem($item);
            }
            $this->setRecollect(true);
        }
        return $this;
    }

    /**
     * Handle data sent from sidebar
     *
     * @param array $data
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function applySidebarData($data)
    {
        if (isset($data['add_order_item'])) {
            foreach ($data['add_order_item'] as $orderItemId => $value) {
                /* @var $orderItem Magento_Sales_Model_Order_Item */
                $orderItem = Mage::getModel('Magento_Sales_Model_Order_Item')->load($orderItemId);
                $item = $this->initFromOrderItem($orderItem);
                if (is_string($item)) {
                    Mage::throwException($item);
                }
            }
        }
        if (isset($data['add_cart_item'])) {
            foreach ($data['add_cart_item'] as $itemId => $qty) {
                $item = $this->getCustomerCart()->getItemById($itemId);
                if ($item) {
                    $this->moveQuoteItem($item, 'order', $qty);
                    $this->removeItem($itemId, 'cart');
                }
            }
        }
        if (isset($data['add_wishlist_item'])) {
            foreach ($data['add_wishlist_item'] as $itemId => $qty) {
                $item = Mage::getModel('Magento_Wishlist_Model_Item')
                    ->loadWithOptions($itemId, 'info_buyRequest');
                if ($item->getId()) {
                    $this->addProduct($item->getProduct(), $item->getBuyRequest()->toArray());
                }
            }
        }
        if (isset($data['add'])) {
            foreach ($data['add'] as $productId => $qty) {
                $this->addProduct($productId, array('qty' => $qty));
            }
        }
        if (isset($data['remove'])) {
            foreach ($data['remove'] as $itemId => $from) {
                $this->removeItem($itemId, $from);
            }
        }
        if (isset($data['empty_customer_cart']) && (int)$data['empty_customer_cart'] == 1) {
            $this->getCustomerCart()->removeAllItems()->collectTotals()->save();
        }
        return $this;
    }

    /**
     * Remove item from some of customer items storage (shopping cart, wishlist etc.)
     *
     * @param   int $itemId
     * @param   string $from
     * @return  Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function removeItem($itemId, $from)
    {
        switch ($from) {
            case 'quote':
                $this->removeQuoteItem($itemId);
                break;
            case 'cart':
                if ($cart = $this->getCustomerCart()) {
                    $cart->removeItem($itemId);
                    $cart->collectTotals()
                        ->save();
                }
                break;
            case 'wishlist':
                if ($wishlist = $this->getCustomerWishlist()) {
                    $item = Mage::getModel('Magento_Wishlist_Model_Item')->load($itemId);
                    $item->delete();
                }
                break;
            case 'compared':
                $item = Mage::getModel('Magento_Catalog_Model_Product_Compare_Item')
                    ->load($itemId)
                    ->delete();
                break;
        }
        return $this;
    }

    /**
     * Remove quote item
     *
     * @param   int $item
     * @return  Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function removeQuoteItem($item)
    {
        $this->getQuote()->removeItem($item);
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Add product to current order quote
     * $product can be either product id or product model
     * $config can be either buyRequest config, or just qty
     *
     * @param   int|Magento_Catalog_Model_Product $product
     * @param   float|array|Magento_Object $config
     * @return  Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function addProduct($product, $config = 1)
    {
        if (!is_array($config) && !($config instanceof Magento_Object)) {
            $config = array('qty' => $config);
        }
        $config = new Magento_Object($config);

        if (!($product instanceof Magento_Catalog_Model_Product)) {
            $productId = $product;
            $product = Mage::getModel('Magento_Catalog_Model_Product')
                ->setStore($this->getSession()->getStore())
                ->setStoreId($this->getSession()->getStoreId())
                ->load($product);
            if (!$product->getId()) {
                Mage::throwException(
                    Mage::helper('Magento_Adminhtml_Helper_Data')->__('We could not add a product to cart by the ID "%s".', $productId)
                );
            }
        }

        $stockItem = $product->getStockItem();
        if ($stockItem && $stockItem->getIsQtyDecimal()) {
            $product->setIsQtyDecimal(1);
        } else {
            $config->setQty((int) $config->getQty());
        }

        $product->setCartQty($config->getQty());
        $item = $this->getQuote()->addProductAdvanced(
            $product,
            $config,
            Magento_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL
        );
        if (is_string($item)) {
            if ($product->getTypeId() != Magento_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                $item = $this->getQuote()->addProductAdvanced(
                    $product,
                    $config,
                    Magento_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_LITE
                );
            }
            if (is_string($item)) {
                Mage::throwException($item);
            }
        }
        $item->checkData();

        $this->setRecollect(true);
        return $this;
    }

    /**
     * Add multiple products to current order quote
     *
     * @param   array $products
     * @return  Magento_Adminhtml_Model_Sales_Order_Create|Exception
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $config) {
            $config['qty'] = isset($config['qty']) ? (float)$config['qty'] : 1;
            try {
                $this->addProduct($productId, $config);
            }
            catch (Magento_Core_Exception $e){
                $this->getSession()->addError($e->getMessage());
            }
            catch (Exception $e){
                return $e;
            }
        }
        return $this;
    }

    /**
     * Update quantity of order quote items
     *
     * @param   array $data
     * @return  Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function updateQuoteItems($data)
    {
        if (is_array($data)) {
            try {
                foreach ($data as $itemId => $info) {
                    if (!empty($info['configured'])) {
                        $item = $this->getQuote()->updateItem($itemId, new Magento_Object($info));
                        $itemQty = (float)$item->getQty();
                    } else {
                        $item       = $this->getQuote()->getItemById($itemId);
                        $itemQty    = (float)$info['qty'];
                    }

                    if ($item) {
                        if ($item->getProduct()->getStockItem()) {
                            if (!$item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                                $itemQty = (int)$itemQty;
                            } else {
                                $item->setIsQtyDecimal(1);
                            }
                        }
                        $itemQty    = $itemQty > 0 ? $itemQty : 1;
                        if (isset($info['custom_price'])) {
                            $itemPrice  = $this->_parseCustomPrice($info['custom_price']);
                        } else {
                            $itemPrice = null;
                        }
                        $noDiscount = !isset($info['use_discount']);

                        if (empty($info['action']) || !empty($info['configured'])) {
                            $item->setQty($itemQty);
                            $item->setCustomPrice($itemPrice);
                            $item->setOriginalCustomPrice($itemPrice);
                            $item->setNoDiscount($noDiscount);
                            $item->getProduct()->setIsSuperMode(true);
                            $item->getProduct()->unsSkipCheckRequiredOption();
                            $item->checkData();
                        }
                        if (!empty($info['action'])) {
                            $this->moveQuoteItem($item, $info['action'], $itemQty);
                        }
                    }
                }
            } catch (Magento_Core_Exception $e) {
                $this->recollectCart();
                throw $e;
            } catch (Exception $e) {
                Mage::logException($e);
            }
            $this->recollectCart();
        }
        return $this;
    }

    /**
     * Parse additional options and sync them with product options
     *
     * @param Magento_Sales_Model_Quote_Item $product
     * @param array $options
     */
    protected function _parseOptions(Magento_Sales_Model_Quote_Item $item, $additionalOptions)
    {
        $productOptions = Mage::getSingleton('Magento_Catalog_Model_Product_Option_Type_Default')
            ->setProduct($item->getProduct())
            ->getProductOptions();

        $newOptions = array();
        $newAdditionalOptions = array();

        foreach (explode("\n", $additionalOptions) as $_additionalOption) {
            if (strlen(trim($_additionalOption))) {
                try {
                    if (strpos($_additionalOption, ':') === false) {
                        Mage::throwException(
                            Mage::helper('Magento_Adminhtml_Helper_Data')->__('There is an error in one of the option rows.')
                        );
                    }
                    list($label,$value) = explode(':', $_additionalOption, 2);
                } catch (Exception $e) {
                    Mage::throwException(Mage::helper('Magento_Adminhtml_Helper_Data')->__('There is an error in one of the option rows.'));
                }
                $label = trim($label);
                $value = trim($value);
                if (empty($value)) {
                    continue;
                }

                if (array_key_exists($label, $productOptions)) {
                    $optionId = $productOptions[$label]['option_id'];
                    $option = $item->getProduct()->getOptionById($optionId);

                    $group = Mage::getSingleton('Magento_Catalog_Model_Product_Option')->groupFactory($option->getType())
                        ->setOption($option)
                        ->setProduct($item->getProduct());

                    $parsedValue = $group->parseOptionValue($value, $productOptions[$label]['values']);

                    if ($parsedValue !== null) {
                        $newOptions[$optionId] = $parsedValue;
                    } else {
                        $newAdditionalOptions[] = array(
                            'label' => $label,
                            'value' => $value
                        );
                    }
                } else {
                    $newAdditionalOptions[] = array(
                        'label' => $label,
                        'value' => $value
                    );
                }
            }
        }

        return array(
            'options' => $newOptions,
            'additional_options' => $newAdditionalOptions
        );
    }

    /**
     * Assign options to item
     *
     * @param Magento_Sales_Model_Quote_Item $item
     * @param array $options
     */
    protected function _assignOptionsToItem(Magento_Sales_Model_Quote_Item $item, $options)
    {
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $item->removeOption('option_'.$optionId);
            }
            $item->removeOption('option_ids');
        }
        if ($item->getOptionByCode('additional_options')) {
            $item->removeOption('additional_options');
        }
        $item->save();
        if (!empty($options['options'])) {
            $item->addOption(new Magento_Object(
                array(
                    'product' => $item->getProduct(),
                    'code' => 'option_ids',
                    'value' => implode(',', array_keys($options['options']))
                )
            ));

            foreach ($options['options'] as $optionId => $optionValue) {
                $item->addOption(new Magento_Object(
                    array(
                        'product' => $item->getProduct(),
                        'code' => 'option_'.$optionId,
                        'value' => $optionValue
                    )
                ));
            }
        }
        if (!empty($options['additional_options'])) {
            $item->addOption(new Magento_Object(
                array(
                    'product' => $item->getProduct(),
                    'code' => 'additional_options',
                    'value' => serialize($options['additional_options'])
                )
            ));
        }

        return $this;
    }

    /**
     * Prepare options array for info buy request
     *
     * @param Magento_Sales_Model_Quote_Item $item
     * @return array
     */
    protected function _prepareOptionsForRequest($item)
    {
        $newInfoOptions = array();
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $item->getProduct()->getOptionById($optionId);
                $optionValue = $item->getOptionByCode('option_'.$optionId)->getValue();

                $group = Mage::getSingleton('Magento_Catalog_Model_Product_Option')->groupFactory($option->getType())
                    ->setOption($option)
                    ->setQuoteItem($item);

                $newInfoOptions[$optionId] = $group->prepareOptionValueForRequest($optionValue);
            }
        }
        return $newInfoOptions;
    }

    protected function _parseCustomPrice($price)
    {
        $price = Mage::app()->getLocale()->getNumber($price);
        $price = $price>0 ? $price : 0;
        return $price;
    }

    /**
     * Retrieve oreder quote shipping address
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Return Customer (Checkout) Form instance
     *
     * @return Magento_Customer_Model_Form
     */
    protected function _getCustomerForm()
    {
        if (is_null($this->_customerForm)) {
            $this->_customerForm = Mage::getModel('Magento_Customer_Model_Form')
                ->setFormCode('adminhtml_checkout')
                ->ignoreInvisible(false);
        }
        return $this->_customerForm;
    }

    /**
     * Return Customer Address Form instance
     *
     * @return Magento_Customer_Model_Form
     */
    protected function _getCustomerAddressForm()
    {
        if (is_null($this->_customerAddressForm)) {
            $this->_customerAddressForm = Mage::getModel('Magento_Customer_Model_Form')
                ->setFormCode('adminhtml_customer_address')
                ->ignoreInvisible(false);
        }
        return $this->_customerAddressForm;
    }

    /**
     * Set and validate Quote address
     * All errors added to _errors
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @param array $data
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected function _setQuoteAddress(Magento_Sales_Model_Quote_Address $address, array $data)
    {
        $addressForm    = $this->_getCustomerAddressForm()
            ->setEntity($address)
            ->setEntityType(Mage::getSingleton('Magento_Eav_Model_Config')->getEntityType('customer_address'))
            ->setIsAjaxRequest(!$this->getIsValidate());

        // prepare request
        // save original request structure for files
        if ($address->getAddressType() == Magento_Sales_Model_Quote_Address::TYPE_SHIPPING) {
            $requestData  = array('order' => array('shipping_address' => $data));
            $requestScope = 'order/shipping_address';
        } else {
            $requestData = array('order' => array('billing_address' => $data));
            $requestScope = 'order/billing_address';
        }
        $request        = $addressForm->prepareRequest($requestData);
        $addressData    = $addressForm->extractData($request, $requestScope);
        if ($this->getIsValidate()) {
            $errors = $addressForm->validateData($addressData);
            if ($errors !== true) {
                if ($address->getAddressType() == Magento_Sales_Model_Quote_Address::TYPE_SHIPPING) {
                    $typeName = Mage::helper('Magento_Adminhtml_Helper_Data')->__('Shipping Address: ');
                } else {
                    $typeName = Mage::helper('Magento_Adminhtml_Helper_Data')->__('Billing Address: ');
                }
                foreach ($errors as $error) {
                    $this->_errors[] = $typeName . $error;
                }
                $addressForm->restoreData($addressData);
            } else {
                $addressForm->compactData($addressData);
            }
        } else {
            $addressForm->restoreData($addressData);
        }

        return $this;
    }

    public function setShippingAddress($address)
    {
        if (is_array($address)) {
            $address['save_in_address_book'] = isset($address['save_in_address_book'])
                && !empty($address['save_in_address_book']);
            $shippingAddress = Mage::getModel('Magento_Sales_Model_Quote_Address')
                ->setData($address)
                ->setAddressType(Magento_Sales_Model_Quote_Address::TYPE_SHIPPING);
            if (!$this->getQuote()->isVirtual()) {
                $this->_setQuoteAddress($shippingAddress, $address);
            }
            $shippingAddress->implodeStreetAddress();
        }
        if ($address instanceof Magento_Sales_Model_Quote_Address) {
            $shippingAddress = $address;
        }

        $this->setRecollect(true);
        $this->getQuote()->setShippingAddress($shippingAddress);
        return $this;
    }

    public function setShippingAsBilling($flag)
    {
        if ($flag) {
            $tmpAddress = clone $this->getBillingAddress();
            $tmpAddress->unsAddressId()
                ->unsAddressType();
            $data = $tmpAddress->getData();
            $data['save_in_address_book'] = 0; // Do not duplicate address (billing address will do saving too)
            $this->getShippingAddress()->addData($data);
        }
        $this->getShippingAddress()->setSameAsBilling($flag);
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Retrieve quote billing address
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }

    public function setBillingAddress($address)
    {
        if (is_array($address)) {
            $address['save_in_address_book'] = isset($address['save_in_address_book']) ? 1 : 0;
            $billingAddress = Mage::getModel('Magento_Sales_Model_Quote_Address')
                ->setData($address)
                ->setAddressType(Magento_Sales_Model_Quote_Address::TYPE_BILLING);
            $this->_setQuoteAddress($billingAddress, $address);
            $billingAddress->implodeStreetAddress();
        }

        if ($this->getShippingAddress()->getSameAsBilling()) {
            $shippingAddress = clone $billingAddress;
            $shippingAddress->setSameAsBilling(true);
            $shippingAddress->setSaveInAddressBook(false);
            $address['save_in_address_book'] = 0;
            $this->setShippingAddress($address);
        }

        $this->getQuote()->setBillingAddress($billingAddress);
        return $this;
    }

    public function setShippingMethod($method)
    {
        $this->getShippingAddress()->setShippingMethod($method);
        $this->setRecollect(true);
        return $this;
    }

    public function resetShippingMethod()
    {
        $this->getShippingAddress()->setShippingMethod(false);
        $this->getShippingAddress()->removeAllShippingRates();
        return $this;
    }

    /**
     * Collect shipping data for quote shipping address
     */
    public function collectShippingRates()
    {
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->collectRates();
        return $this;
    }

    public function collectRates()
    {
        $this->getQuote()->collectTotals();
    }

    public function setPaymentMethod($method)
    {
        $this->getQuote()->getPayment()->setMethod($method);
        return $this;
    }

    public function setPaymentData($data)
    {
        if (!isset($data['method'])) {
            $data['method'] = $this->getQuote()->getPayment()->getMethod();
        }
        $this->getQuote()->getPayment()->importData($data);
        return $this;
    }

    public function applyCoupon($code)
    {
        $code = trim((string)$code);
        $this->getQuote()->setCouponCode($code);
        $this->setRecollect(true);
        return $this;
    }

    public function setAccountData($accountData)
    {
        $customer   = $this->getQuote()->getCustomer();
        $form       = $this->_getCustomerForm();
        $form->setEntity($customer);

        // emulate request
        $request = $form->prepareRequest($accountData);
        $data    = $form->extractData($request);
        $form->restoreData($data);

        $data = array();
        foreach ($form->getAttributes() as $attribute) {
            $code = sprintf('customer_%s', $attribute->getAttributeCode());
            $data[$code] = $customer->getData($attribute->getAttributeCode());
        }

        if (isset($data['customer_group_id'])) {
            $groupModel = Mage::getModel('Magento_Customer_Model_Group')->load($data['customer_group_id']);
            $data['customer_tax_class_id'] = $groupModel->getTaxClassId();
            $this->setRecollect(true);
        }

        $this->getQuote()->addData($data);
        return $this;
    }

    /**
     * Parse data retrieved from request
     *
     * @param   array $data
     * @return  Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function importPostData($data)
    {
        if (is_array($data)) {
            $this->addData($data);
        } else {
            return $this;
        }

        if (isset($data['account'])) {
            $this->setAccountData($data['account']);
        }

        if (isset($data['comment'])) {
            $this->getQuote()->addData($data['comment']);
            if (empty($data['comment']['customer_note_notify'])) {
                $this->getQuote()->setCustomerNoteNotify(false);
            } else {
                $this->getQuote()->setCustomerNoteNotify(true);
            }
        }

        if (isset($data['billing_address'])) {
            $this->setBillingAddress($data['billing_address']);
        }

        if (isset($data['shipping_address'])) {
            $this->setShippingAddress($data['shipping_address']);
        }

        if (isset($data['shipping_method'])) {
            $this->setShippingMethod($data['shipping_method']);
        }

        if (isset($data['payment_method'])) {
            $this->setPaymentMethod($data['payment_method']);
        }

        if (isset($data['coupon']['code'])) {
            $this->applyCoupon($data['coupon']['code']);
        }
        return $this;
    }

    /**
     * Check whether we need to create new customer (for another website) during order creation
     *
     * @param   Magento_Core_Model_Store $store
     * @return  boolean
     */
    protected function _customerIsInStore($store)
    {
        $customer = $this->getSession()->getCustomer();
        if ($customer->getWebsiteId() == $store->getWebsiteId()) {
            return true;
        }
        return $customer->isInStore($store);
    }

    /**
     * Set and validate Customer data
     *
     * @param Magento_Customer_Model_Customer $customer
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected function _setCustomerData(Magento_Customer_Model_Customer $customer)
    {
        $form = $this->_getCustomerForm();
        $form->setEntity($customer);

        // emulate request
        $request = $form->prepareRequest(array('order' => $this->getData()));
        $data    = $form->extractData($request, 'order/account');
        if ($this->getIsValidate()) {
            $errors = $form->validateData($data);
            if ($errors !== true) {
                foreach ($errors as $error) {
                    $this->_errors[] = $error;
                }
                $form->restoreData($data);
            } else {
                $form->compactData($data);
            }
        } else {
            $form->restoreData($data);
        }

        return $this;
    }

    /**
     * Prepare quote customer
     *
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function _prepareCustomer()
    {
        /** @var $quote Magento_Sales_Model_Quote */
        $quote = $this->getQuote();
        if ($quote->getCustomerIsGuest()) {
            return $this;
        }

        /** @var $customer Magento_Customer_Model_Customer */
        $customer = $this->getSession()->getCustomer();
        /** @var $store Magento_Core_Model_Store */
        $store = $this->getSession()->getStore();

        $customerIsInStore = $this->_customerIsInStore($store);
        $customerBillingAddress = null;
        $customerShippingAddress = null;

        if ($customer->getId()) {
            // Create new customer if customer is not registered in specified store
            if (!$customerIsInStore) {
                $customer->setId(null)
                    ->setStore($store)
                    ->setDefaultBilling(null)
                    ->setDefaultShipping(null)
                    ->setPassword($customer->generatePassword());
                $this->_setCustomerData($customer);
            }

            if ($this->getBillingAddress()->getSaveInAddressBook()) {
                /** @var $customerBillingAddress Magento_Customer_Model_Address */
                $customerBillingAddress = $this->getBillingAddress()->exportCustomerAddress();
                $customerAddressId = $this->getBillingAddress()->getCustomerAddressId();
                if ($customerAddressId && $customer->getId()) {
                    $customer->getAddressItemById($customerAddressId)->addData($customerBillingAddress->getData());
                } else {
                    $customer->addAddress($customerBillingAddress);
                }
            }

            if (!$this->getQuote()->isVirtual() && $this->getShippingAddress()->getSaveInAddressBook()) {
                /** @var $customerShippingAddress Magento_Customer_Model_Address */
                $customerShippingAddress = $this->getShippingAddress()->exportCustomerAddress();
                $customerAddressId = $this->getShippingAddress()->getCustomerAddressId();
                if ($customerAddressId && $customer->getId()) {
                    $customer->getAddressItemById($customerAddressId)->addData($customerShippingAddress->getData());
                } elseif (!empty($customerAddressId)
                    && $customerBillingAddress !== null
                    && $this->getBillingAddress()->getCustomerAddressId() == $customerAddressId
                ) {
                    $customerBillingAddress->setIsDefaultShipping(true);
                } else {
                    $customer->addAddress($customerShippingAddress);
                }
            }

            if (is_null($customer->getDefaultBilling()) && $customerBillingAddress) {
                $customerBillingAddress->setIsDefaultBilling(true);
            }

            if (is_null($customer->getDefaultShipping())) {
                if ($this->getShippingAddress()->getSameAsBilling() && $customerBillingAddress) {
                    $customerBillingAddress->setIsDefaultShipping(true);
                } elseif ($customerShippingAddress) {
                    $customerShippingAddress->setIsDefaultShipping(true);
                }
            }
        } else {
            // Prepare new customer
            /** @var $customerBillingAddress Magento_Customer_Model_Address */
            $customerBillingAddress = $this->getBillingAddress()->exportCustomerAddress();
            $customer->addData($customerBillingAddress->getData())
                ->setPassword($customer->generatePassword())
                ->setStore($store);
            $customer->setEmail($this->_getNewCustomerEmail($customer));
            $this->_setCustomerData($customer);

            if ($this->getBillingAddress()->getSaveInAddressBook()) {
                $customerBillingAddress->setIsDefaultBilling(true);
                $customer->addAddress($customerBillingAddress);
            }

            /** @var $shippingAddress Magento_Sales_Model_Quote_Address */
            $shippingAddress = $this->getShippingAddress();
            if (!$this->getQuote()->isVirtual()
                && !$shippingAddress->getSameAsBilling()
                && $shippingAddress->getSaveInAddressBook()
            ) {
                /** @var $customerShippingAddress Magento_Customer_Model_Address */
                $customerShippingAddress = $shippingAddress->exportCustomerAddress();
                $customerShippingAddress->setIsDefaultShipping(true);
                $customer->addAddress($customerShippingAddress);
            } else {
                $customerBillingAddress->setIsDefaultShipping(true);
            }
        }

        // Set quote customer data to customer
        $this->_setCustomerData($customer);

        // Set customer to quote and convert customer data to quote
        $quote->setCustomer($customer);

        // Add user defined attributes to quote
        $form = $this->_getCustomerForm()->setEntity($customer);
        foreach ($form->getUserAttributes() as $attribute) {
            $quoteCode = sprintf('customer_%s', $attribute->getAttributeCode());
            $quote->setData($quoteCode, $customer->getData($attribute->getAttributeCode()));
        }

        if ($customer->getId()) {
            // Restore account data for existing customer
            $this->_getCustomerForm()
                ->setEntity($customer)
                ->resetEntityData();
        } else {
            $quote->setCustomerId(true);
        }

        return $this;
    }

    /**
     * Prepare item otions
     */
    protected function _prepareQuoteItems()
    {
        foreach ($this->getQuote()->getAllItems() as $item) {
            $options = array();
            $productOptions = $item->getProduct()->getTypeInstance()->getOrderOptions($item->getProduct());
            if ($productOptions) {
                $productOptions['info_buyRequest']['options'] = $this->_prepareOptionsForRequest($item);
                $options = $productOptions;
            }
            $addOptions = $item->getOptionByCode('additional_options');
            if ($addOptions) {
                $options['additional_options'] = unserialize($addOptions->getValue());
            }
            $item->setProductOrderOptions($options);
        }
        return $this;
    }

    /**
     * Create new order
     *
     * @return Magento_Sales_Model_Order
     */
    public function createOrder()
    {
        $this->_prepareCustomer();
        $this->_validate();
        $quote = $this->getQuote();
        $this->_prepareQuoteItems();

        /** @var $service Magento_Sales_Model_Service_Quote */
        $service = Mage::getModel('Magento_Sales_Model_Service_Quote', array('quote' => $quote));
        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();
            $originalId = $oldOrder->getOriginalIncrementId();
            if (!$originalId) {
                $originalId = $oldOrder->getIncrementId();
            }
            $orderData = array(
                'original_increment_id'     => $originalId,
                'relation_parent_id'        => $oldOrder->getId(),
                'relation_parent_real_id'   => $oldOrder->getIncrementId(),
                'edit_increment'            => $oldOrder->getEditIncrement()+1,
                'increment_id'              => $originalId.'-'.($oldOrder->getEditIncrement()+1)
            );
            $quote->setReservedOrderId($orderData['increment_id']);
            $service->setOrderData($orderData);
        }

        $order = $service->submitOrder();
        if ((!$quote->getCustomer()->getId() || !$quote->getCustomer()->isInStore($this->getSession()->getStore()))
            && !$quote->getCustomerIsGuest()
        ) {
            $quote->getCustomer()->setCreatedAt($order->getCreatedAt());
            $quote->getCustomer()
                ->save()
                ->sendNewAccountEmail('registered', '', $quote->getStoreId());;
        }
        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();

            $this->getSession()->getOrder()->setRelationChildId($order->getId());
            $this->getSession()->getOrder()->setRelationChildRealId($order->getIncrementId());
            $this->getSession()->getOrder()->cancel()
                ->save();
            $order->save();
        }
        if ($this->getSendConfirmation()) {
            $order->sendNewOrderEmail();
        }

        Mage::dispatchEvent('checkout_submit_all_after', array('order' => $order, 'quote' => $quote));

        return $order;
    }

    /**
     * Validate quote data before order creation
     *
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected function _validate()
    {
        $customerId = $this->getSession()->getCustomerId();
        if (is_null($customerId)) {
            Mage::throwException(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Please select a customer.'));
        }

        if (!$this->getSession()->getStore()->getId()) {
            Mage::throwException(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Please select a store.'));
        }
        $items = $this->getQuote()->getAllItems();

        if (count($items) == 0) {
            $this->_errors[] = Mage::helper('Magento_Adminhtml_Helper_Data')->__('You need to specify order items.');
        }

        foreach ($items as $item) {
            $messages = $item->getMessage(false);
            if ($item->getHasError() && is_array($messages) && !empty($messages)) {
                $this->_errors = array_merge($this->_errors, $messages);
            }
        }

        if (!$this->getQuote()->isVirtual()) {
            if (!$this->getQuote()->getShippingAddress()->getShippingMethod()) {
                $this->_errors[] = Mage::helper('Magento_Adminhtml_Helper_Data')->__('You need to specify a shipping method.');
            }
        }

        if (!$this->getQuote()->getPayment()->getMethod()) {
            $this->_errors[] = Mage::helper('Magento_Adminhtml_Helper_Data')->__('A payment method must be specified.');
        } else {
            $method = $this->getQuote()->getPayment()->getMethodInstance();
            if (!$method) {
                $this->_errors[] = Mage::helper('Magento_Adminhtml_Helper_Data')->__('This payment method instance is not available.');
            } else {
                if (!$method->isAvailable($this->getQuote())) {
                    $this->_errors[] = Mage::helper('Magento_Adminhtml_Helper_Data')->__('This payment method is not available.');
                } else {
                    try {
                        $method->validate();
                    } catch (Magento_Core_Exception $e) {
                        $this->_errors[] = $e->getMessage();
                    }
                }
            }
        }

        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                $this->getSession()->addError($error);
            }
            Mage::throwException('');
        }
        return $this;
    }

    /**
     * Retrieve new customer email
     *
     * @param   Magento_Customer_Model_Customer $customer
     * @return  string
     */
    protected function _getNewCustomerEmail($customer)
    {
        $email = $this->getData('account/email');
        if (empty($email)) {
            $host = $this->getSession()
                ->getStore()
                ->getConfig(Magento_Customer_Model_Customer::XML_PATH_DEFAULT_EMAIL_DOMAIN);
            $account = $customer->getIncrementId() ? $customer->getIncrementId() : time();
            $email = $account.'@'. $host;
            $account = $this->getData('account');
            $account['email'] = $email;
            $this->setData('account', $account);
        }
        return $email;
    }
}
