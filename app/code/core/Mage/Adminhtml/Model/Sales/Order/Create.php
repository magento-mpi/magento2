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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order create model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Sales_Order_Create extends Varien_Object
{
    /**
     * Quote session object
     *
     * @var Mage_Adminhtml_Model_Session_Quote
     */
    protected $_session;

    /**
     * Quote customer wishlist model object
     *
     * @var Mage_Wishlist_Model_Wishlist
     */
    protected $_wishlist;
    protected $_cart;
    protected $_compareList;

    protected $_needCollect;

    protected  $_productOptions = array();

    public function __construct()
    {
        $this->_session = Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve quote item
     *
     * @param   mixed $item
     * @return  Mage_Sales_Model_Quote_Item
     */
    protected function _getQuoteItem($item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            return $item;
        }
        elseif (is_numeric($item)) {
            return $this->getSession()->getQuote()->getItemById($item);
        }
        return false;
    }

    /**
     * Initialize data for prise rules
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function initRuleData()
    {
        Mage::register('rule_data', new Varien_Object(array(
            'store_id'  => $this->_session->getStore()->getId(),
            'customer_group_id' => $this->getCustomerGroupId(),
        )));
        return $this;
    }

    /**
     * Set collect totals flag for quote
     *
     * @param   bool $flag
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function setRecollect($flag)
    {
        $this->_needCollect = $flag;
        return $this;
    }

    /**
     * Quote saving
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
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
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Retrieve quote object model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getSession()->getQuote();
    }

    /**
     * Initialize creation data from existing order
     *
     * @param Mage_Sales_Model_Order $order
     * @return unknown
     */
    public function initFromOrder(Mage_Sales_Model_Order $order)
    {
        if (!$order->getReordered()) {
            $this->getSession()->setOrderId($order->getId());
        } else {
            $this->getSession()->setReordered($order->getId());
        }

        $this->getSession()->setCurrencyId($order->getOrderCurrencyCode());
        $this->getSession()->setCustomerId($order->getCustomerId());
        $this->getSession()->setStoreId($order->getStoreId());

        foreach ($order->getItemsCollection() as $orderItem) {
            /* @var $orderItem Mage_Sales_Model_Order_Item */

            $product = Mage::getModel('catalog/product')
                ->setStoreId($this->getSession()->getStoreId())
                ->load($orderItem->getProductId());

            if ($product->getId()) {
                $info = $orderItem->getProductOptionByCode('info_buyRequest');
                $info = new Varien_Object($info);
                $product->setSkipCheckRequiredOption(true);
                $item = $this->getQuote()->addProduct($product,$info);
                if (is_string($item)) {
                    Mage::throwException($item);
                }
                $item->setQty($orderItem->getQtyOrdered());
                if ($addOptions = $orderItem->getProductOptionByCode('additional_options')) {
                    $item->addOption(new Varien_Object(
                        array(
                            'product' => $item->getProduct(),
                            'code' => 'additional_options',
                            'value' => serialize($addOptions)
                        )
                    ));
                }
            }
        }

        $this->getQuote()->collectTotals()
            ->save();

//        $convertModel = Mage::getModel('sales/convert_order');
//        /*@var $quote Mage_Sales_Model_Quote*/
//        $quote = $convertModel->toQuote($order, $this->getQuote());
//        $quote->setShippingAddress($convertModel->toQuoteShippingAddress($order));
//        $quote->setBillingAddress($convertModel->addressToQuoteAddress($order->getBillingAddress()));
//
//        if ($order->getReordered()) {
//            $quote->getPayment()->setMethod($order->getPayment()->getMethod());
//        }
//        else {
//            $convertModel->paymentToQuotePayment($order->getPayment(), $quote->getPayment());
//        }
//
//        foreach ($order->getItemsCollection() as $item) {
//            if ($order->getReordered()) {
//                $qty = $item->getQtyOrdered();
//            }
//            else {
//                $qty = min($item->getQtyToInvoice(), $item->getQtyToShip());
//            }
//            if ($qty) {
//                $quoteItem = $convertModel->itemToQuoteItem($item)
//                    ->setQuote($quote)
//                    ->setQty($qty);
//                $product = $quoteItem->getProduct();
//
//                if ($product->getId()) {
//                    $quote->addItem($quoteItem);
//                }
//            }
//        }


//        if ($quote->getCouponCode()) {
//            $quote->collectTotals();
//        }
//
//        $quote->getShippingAddress()->setCollectShippingRates(true);
//        $quote->getShippingAddress()->collectShippingRates();
//        $quote->collectTotals();
//        $quote->save();

        return $this;
    }

    /**
     * Retrieve customer wishlist model object
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getCustomerWishlist()
    {
        if (!is_null($this->_wishlist)) {
            return $this->_wishlist;
        }

        if ($this->getSession()->getCustomer()->getId()) {
            $this->_wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer(
                $this->getSession()->getCustomer(), true
            );
            $this->_wishlist->setStore($this->getSession()->getStore());
        }
        else {
            $this->_wishlist = false;
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve customer cart quote object model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getCustomerCart()
    {
        if (!is_null($this->_cart)) {
            return $this->_cart;
        }

        $this->_cart = Mage::getModel('sales/quote');

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
     * @return Mage_Catalog_Model_Product_Compare_List
     */
    public function getCustomerCompareList()
    {
        if (!is_null($this->_compareList)) {
            return $this->_compareList;
        }

        if ($this->getSession()->getCustomer()->getId()) {
            $this->_compareList = Mage::getModel('catalog/product_compare_list');
        }
        else {
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
     * Move quote item to another items store
     *
     * @param   mixed $item
     * @param   string $mogeTo
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function moveQuoteItem($item, $moveTo, $qty)
    {
        if ($item = $this->_getQuoteItem($item)) {
            switch ($moveTo) {
                case 'order':
                    $info = array();
                    if ($info = $item->getOptionByCode('info_buyRequest')) {
                        $info = new Varien_Object(
                            unserialize($info->getValue())
                        );
                        $info->setOptions($this->_prepareOptionsForRequest($item));
                    }

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($this->getQuote()->getStoreId())
                        ->load($item->getProduct()->getId());

                    $product->setSkipCheckRequiredOption(true);

                    $newItem = $this->getQuote()->addProduct($product, $info);
                    if (is_string($newItem)) {
                        Mage::throwException($newItem);
                    }
                    $product->unsSkipCheckRequiredOption();
                    $newItem->checkData();
                    $newItem->setQty($qty);
                    $this->getQuote()->collectTotals()
                        ->save();
                    break;
                case 'cart':
                    if (($cart = $this->getCustomerCart()) && is_null($item->getOptionByCode('additional_options'))) {
                        //options and info buy request
                        $product = Mage::getModel('catalog/product')
                            ->setStoreId($this->getQuote()->getStoreId())
                            ->load($item->getProduct()->getId());
                        $product->setSkipCheckRequiredOption(true);

                        $info = array();
                        if ($info = $item->getOptionByCode('info_buyRequest')) {
                            $info = new Varien_Object(
                                unserialize($info->getValue())
                            );
                            $info->setOptions($this->_prepareOptionsForRequest($item));
                        } else {
                            $info = new Varien_Object(array(
                                'product_id' => $product->getId(),
                                'qty' => $qty,
                                'options' => $this->_prepareOptionsForRequest($item)
                            ));
                        }

                        $cartItem = $cart->addProduct($product, $info);
                        if (is_string($cartItem)) {
                            Mage::throwException($cartItem);
                        }
                        $product->unsSkipCheckRequiredOption();
                        $cartItem->setQty($qty);
                        $cartItem->setPrice($item->getProduct()->getPrice());
                        $cart->collectTotals()
                            ->save();
                    }
                    break;
                case 'wishlist':
                    if ($wishlist = $this->getCustomerWishlist()) {
                        $wishlist->addNewItem($item->getProduct()->getId());
                    }
                    break;
                case 'comparelist':

                    break;
                default:
                    break;
            }
            $this->getQuote()->removeItem($item->getId());
            $this->setRecollect(true);
        }
        return $this;
    }

    public function applySidebarData($data)
    {
        if (isset($data['add'])) {
            foreach ($data['add'] as $itemId=>$qty) {
                $item = $this->getCustomerCart()->getItemById($itemId);
                $this->moveQuoteItem($item, 'order', $qty);
                $this->removeItem($itemId, 'cart');
            }
        }
        if (isset($data['remove'])) {
            foreach ($data['remove'] as $itemId => $from) {
                $this->removeItem($itemId, $from);
            }
        }
        return $this;
    }

    /**
     * Remove item from some of customer items storage (shopping cart, wishlist etc.)
     *
     * @param   int $itemId
     * @param   string $from
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
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
                    $item = Mage::getModel('wishlist/item')->load($itemId);
                    $item->delete();
                }
                break;
            case 'compared':
                $item = Mage::getModel('catalog/product_compare_item')
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
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function removeQuoteItem($item)
    {
        $this->getQuote()->removeItem($item);
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Add product to current order quote
     *
     * @param   mixed $product
     * @param   mixed $qty
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function addProduct($product, $qty=1)
    {
        $qty = (int) $qty;
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            $product = Mage::getModel('catalog/product')
                ->setStore($this->getSession()->getStore())
                ->load($product);
        }

        if ($item = $this->getQuote()->getItemByProduct($product)) {
            $item->setQty($item->getQty()+$qty);
        }
        else {
            $product->setSkipCheckRequiredOption(true);
            $item = $this->getQuote()->addProduct($product, $qty);
            $product->unsSkipCheckRequiredOption();
            $item->checkData();
        }

        $this->setRecollect(true);
        return $this;
    }

    /**
     * Add multiple products to current order quote
     *
     * @param   array $products
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $data) {
            $qty = isset($data['qty']) ? (int)$data['qty'] : 1;
            try {
                $this->addProduct($productId, $qty);
            }
            catch (Mage_Core_Exception $e){
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
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function updateQuoteItems($data)
    {
        if (is_array($data)) {
            foreach ($data as $itemId => $info) {
                $itemQty    = (int) $info['qty'];
                $itemQty    = $itemQty>0 ? $itemQty : 1;
                if (isset($info['custom_price'])) {
                    $itemPrice  = $this->_parseCustomPrice($info['custom_price']);
                }
                else {
                    $itemPrice = null;
                }
                $noDiscount = !isset($info['use_discount']);

//                if ($item = $this->getQuote()->getItemById($itemId)) {
//                    $this->_assignOptionsToItem(
//                        $item,
//                        $this->_parseOptions($item, $info['options'])
//                    );
//                    if (empty($info['action'])) {
//                        $item->setQty($itemQty);
//                        $item->setCustomPrice($itemPrice);
//                        $item->setNoDiscount($noDiscount);
//                    }
//                    else {
//                        $this->moveQuoteItem($item, $info['action'], $itemQty);
//                    }
//                }

                if (empty($info['action'])) {
                    if ($item = $this->getQuote()->getItemById($itemId)) {

                        $item->setQty($itemQty);
                        $item->setCustomPrice($itemPrice);
                        $item->setNoDiscount($noDiscount);
                        $item->getProduct()->setIsSuperMode(true);

                        $this->_assignOptionsToItem(
                            $item,
                            $this->_parseOptions($item, $info['options'])
                        );
                        $item->checkData();
                    }
                }
                else {
                    $this->moveQuoteItem($itemId, $info['action'], $itemQty);
                }
            }
            $this->setRecollect(true);
        }
        return $this;
    }

    /**
     * Parse additional options and sync them with product options
     *
     * @param Mage_Sales_Model_Quote_Item $product
     * @param array $options
     */
    protected function _parseOptions(Mage_Sales_Model_Quote_Item $item, $additionalOptions)
    {
        if (!isset($this->_productOptions[$item->getProduct()->getId()])) {
            foreach ($item->getProduct()->getOptions() as $_option) {
                /* @var $option Mage_Catalog_Model_Product_Option */
                $this->_productOptions[$item->getProduct()->getId()][$_option->getTitle()] = array('option_id' => $_option->getId());
                if ($_option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                    $optionValues = array();
                    foreach ($_option->getValues() as $_value) {
                        /* @var $value Mage_Catalog_Model_Product_Option_Value */
                        $optionValues[$_value->getTitle()] = $_value->getId();
                    }
                    $this->_productOptions[$item->getProduct()->getId()][$_option->getTitle()]['values'] = $optionValues;
                } else {
                    $this->_productOptions[$item->getProduct()->getId()][$_option->getTitle()]['values'] = array();
                }
            }
        }

        $newOptions = array();
        $newAdditionalOptions = array();
        foreach (explode("\n", $additionalOptions) as $_additionalOption) {
            if (strlen(trim($_additionalOption))) {
                try {
                    list($label,$value) = explode(':', $_additionalOption);
                } catch (Exception $e) {
                    Mage::throwException(Mage::helper('adminhtml')->__('One of options row has error'));
                }
                $label = trim($label);
                $value = trim($value);
                if (empty($value)) {
                    continue;
//                    Mage::throwException(Mage::helper('adminhtml')->__('Please add values for options'));
                }

                if (array_key_exists($label, $this->_productOptions[$item->getProduct()->getId()])) {
                    $optionId = $this->_productOptions[$item->getProduct()->getId()][$label]['option_id'];
                    $group = $item->getProduct()
                            ->getOptionById($optionId)
                            ->getGroupByType();
                    $type = $item->getProduct()
                            ->getOptionById($optionId)
                            ->getType();
                    if (($type == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
                        || $type == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE)) {
                        $_values = array();
                        foreach (explode(',', $value) as $_value) {
                            $_value = trim($_value);
                            if (array_key_exists($_value, $this->_productOptions[$item->getProduct()->getId()][$label]['values'])) {
                                $_values[] = $this->_productOptions[$item->getProduct()->getId()][$label]['values'][$_value];
                            } else {
                                $_values = array();
                                $newAdditionalOptions[] = array(
                                    'label' => $label,
                                    'value' => $value
                                );
                                break;
                            }
                        }
                        if (count($_values)) {
                            $newOptions[$optionId] = implode(',',$_values);
                        }
                    } elseif ($group == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                        if (array_key_exists($value, $this->_productOptions[$item->getProduct()->getId()][$label]['values'])) {
                            $newOptions[$optionId] = $this->_productOptions[$item->getProduct()->getId()][$label]['values'][$value];
                        } else {
                            $newAdditionalOptions[] = array(
                                'label' => $label,
                                'value' => $value
                            );
                        }
                    } else {
                        $newOptions[$optionId] = $value;
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
     * @param Mage_Sales_Model_Quote_Item $item
     * @param array $options
     */
    protected function _assignOptionsToItem(Mage_Sales_Model_Quote_Item $item, $options)
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
            $item->addOption(new Varien_Object(
                array(
                    'product' => $item->getProduct(),
                    'code' => 'option_ids',
                    'value' => implode(',', array_keys($options['options']))
                )
            ));

            foreach ($options['options'] as $optionId => $optionValue) {
                $item->addOption(new Varien_Object(
                    array(
                        'product' => $item->getProduct(),
                        'code' => 'option_'.$optionId,
                        'value' => $optionValue
                    )
                ));
            }
        }
        if (!empty($options['additional_options'])) {
            $item->addOption(new Varien_Object(
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
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    protected function _prepareOptionsForRequest($item)
    {
        $newInfoOptions = array();
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $optionType = $item->getProduct()->getOptionById($optionId)->getType();
                $optionValue = $item->getOptionByCode('option_'.$optionId)->getValue();
                if ($optionType == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
                    || $optionType == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                    $optionValue = explode(',', $optionValue);
                }
                $newInfoOptions[$optionId] = $optionValue;
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
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    public function setShippingAddress($address)
    {
        if (is_array($address)) {
            $address['save_in_address_book'] = isset($address['save_in_address_book']) ? 1 : 0;
            $shippingAddress = Mage::getModel('sales/quote_address')
                ->setData($address);
            $shippingAddress->implodeStreetAddress();
        }
        if ($address instanceof Mage_Sales_Model_Quote_Address) {
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
            $tmpAddress->unsEntityId()
                ->unsAddressType();
            $this->getShippingAddress()->addData($tmpAddress->getData());
        }
        $this->getShippingAddress()->setSameAsBilling($flag);
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Retrieve quote billing address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }

    public function setBillingAddress($address)
    {
        if (is_array($address)) {
            $address['save_in_address_book'] = isset($address['save_in_address_book']) ? 1 : 0;
            $billingAddress = Mage::getModel('sales/quote_address')
                ->setData($address);
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

    public function collectShippingRates()
    {
        $this->collectRates();
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getQuote()->getShippingAddress()->collectShippingRates();
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
        $data = array();
        foreach ($accountData as $key => $value) {
            $data['customer_'.$key] = $value;
        }

        if (isset($data['customer_group_id'])) {
            $groupModel = Mage::getModel('customer/group')->load($data['customer_group_id']);
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
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function importPostData($data)
    {
        $this->addData($data);

        if (isset($data['account'])) {
            $this->setAccountData($data['account']);
        }

        if (isset($data['comment'])) {
            $this->getQuote()->addData($data['comment']);
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
     * Create new order
     *
     * @return Mage_Sales_Model_Order
     */
    public function createOrder()
    {
        $this->_validate();
        if (!$this->getQuote()->getCustomerIsGuest()) {
            $this->_saveCustomer();
        }

        $quoteConvert = Mage::getModel('sales/convert_quote');

        /* @var $quoteConvert Mage_Sales_Model_Convert_Quote */

        $quote = $this->getQuote();

        if ($this->getQuote()->getIsVirtual()) {
            $order = $quoteConvert->addressToOrder($quote->getBillingAddress());
        }
        else {
            $order = $quoteConvert->addressToOrder($quote->getShippingAddress());
        }
        $order->setBillingAddress($quoteConvert->addressToOrderAddress($quote->getBillingAddress()))
            ->setPayment($quoteConvert->paymentToOrderPayment($quote->getPayment()));
        if (!$this->getQuote()->getIsVirtual()) {
            $order->setShippingAddress($quoteConvert->addressToOrderAddress($quote->getShippingAddress()));
        }

        if (!$this->getQuote()->getIsVirtual()) {
            foreach ($quote->getShippingAddress()->getAllItems() as $item) {
                /* @var $item Mage_Sales_Model_Quote_Item */
                $orderItem = $quoteConvert->itemToOrderItem($item);
                $options = array();
                if ($productOptions = $item->getProduct()->getTypeInstance()->getOrderOptions()) {
                    $productOptions['info_buyRequest']['options'] = $this->_prepareOptionsForRequest($item);
                    $options = $productOptions;
                }
                if ($addOptions = $item->getOptionByCode('additional_options')) {
                    $options['additional_options'] = unserialize($addOptions->getValue());
                }
                if ($options) {
                    $orderItem->setProductOptions($options);
                }
                $order->addItem($orderItem);
            }
        }
        if ($this->getQuote()->hasVirtualItems()) {
            foreach ($quote->getBillingAddress()->getAllItems() as $item) {
                /* @var $item Mage_Sales_Model_Quote_Item */
                $orderItem = $quoteConvert->itemToOrderItem($item);
                $options = array();
                if ($productOptions = $item->getProduct()->getTypeInstance()->getOrderOptions()) {
                    $productOptions['info_buyRequest']['options'] = $this->_prepareOptionsForRequest($item);
                    $options = $productOptions;
                }
                if ($addOptions = $item->getOptionByCode('additional_options')) {
                    $options['additional_options'] = unserialize($addOptions->getValue());
                }
                if ($options) {
                    $orderItem->setProductOptions($options);
                }
                $order->addItem($orderItem);
            }
        }

        if ($this->getSendConfirmation()) {
            $order->setEmailSent(true);
        }

        $order->place()
            ->save();

        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();
            $originalId = $oldOrder->getOriginalIncrementId() ? $oldOrder->getOriginalIncrementId() : $oldOrder->getIncrementId();
            $order->setOriginalIncrementId($originalId);
            $order->setRelationParentId($oldOrder->getId());
            $order->setRelationParentRealId($oldOrder->getIncrementId());
            $order->setEditIncrement($oldOrder->getEditIncrement()+1);
            $order->setIncrementId($originalId.'-'.$order->getEditIncrement());

            $this->getSession()->getOrder()->setRelationChildId($order->getId());
            $this->getSession()->getOrder()->setRelationChildRealId($order->getIncrementId());
            $this->getSession()->getOrder()->cancel()
                ->save();
            $order->save();
        }

        if ($this->getSendConfirmation()) {
            $order->sendNewOrderEmail();
        }

        return $order;
    }

    /**
     * Validate quote data before order creation
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _validate()
    {
        $customerId = $this->getSession()->getCustomerId();
        if (is_null($customerId)) {
            Mage::throwException(Mage::helper('adminhtml')->__('Please select a custmer'));
        }

        if (!$this->getSession()->getStore()->getId()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Please select a store'));
        }
        $items = $this->getQuote()->getAllItems();

        $errors = array();
        if (count($items) == 0) {
            $errors[] = Mage::helper('adminhtml')->__('You need specify order items');
        }

        if (!$this->getQuote()->isVirtual()) {
            if (!$this->getQuote()->getShippingAddress()->getShippingMethod()) {
                $errors[] = Mage::helper('adminhtml')->__('Shipping method must be specified');
            }

            if (!$this->getQuote()->getPayment()->getMethod()) {
                $errors[] = Mage::helper('adminhtml')->__('Payment method must be specified');
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->getSession()->addError($error);
            }
            Mage::throwException('');
        }
        return $this;
    }

    /**
     * Save order customer account data
     *
     * @return unknown
     */
    protected function _saveCustomer()
    {
        if (!$this->getSession()->getCustomer()->getId()) {
            $customer = Mage::getModel('customer/customer');
            /* @var $customer Mage_Customer_Model_Customer*/

            $billingAddress = $this->getBillingAddress()->exportCustomerAddress();

            $customer->addData($billingAddress->getData())
                ->addData($this->getData('account'))
                ->setPassword($customer->generatePassword())
                ->setWebsiteId($this->getSession()->getStore()->getWebsiteId())
                ->setStoreId($this->getSession()->getStore()->getId())
                ->addAddress($billingAddress);

            if (!$this->getShippingAddress()->getSameAsBilling()) {
                $shippingAddress = $this->getShippingAddress()->exportCustomerAddress();
                $customer->addAddress($shippingAddress);
            }
            else {
                $shippingAddress = $billingAddress;
            }
            $customer->save();


            $customer->setEmail($this->_getNewCustomerEmail($customer))
                ->setDefaultBilling($billingAddress->getId())
                ->setDefaultShipping($shippingAddress->getId())
                ->save();

            $this->getBillingAddress()->setCustomerId($customer->getId());
            $this->getShippingAddress()->setCustomerId($customer->getId());

            $customer->sendNewAccountEmail();
        }
        else {
            $customer = $this->getSession()->getCustomer();

            $saveCusstomerAddress = false;

            if ($this->getBillingAddress()->getSaveInAddressBook()) {
                $billingAddress = $this->getBillingAddress()->exportCustomerAddress();
                if ($this->getBillingAddress()->getCustomerAddressId()) {
                    $billingAddress->setId($this->getBillingAddress()->getCustomerAddressId());
                }
                $customer->addAddress($billingAddress);
                $saveCusstomerAddress = true;
            }
            if ($this->getShippingAddress()->getSaveInAddressBook()) {
                $shippingAddress = $this->getShippingAddress()->exportCustomerAddress();
                if ($this->getShippingAddress()->getCustomerAddressId()) {
                    $shippingAddress->setId($this->getShippingAddress()->getCustomerAddressId());
                }
                $customer->addAddress($shippingAddress);
                $saveCusstomerAddress = true;
            }
            if ($saveCusstomerAddress) {
                $customer->save();
            }

            $customer->addData($this->getData('account'));
            /**
             * don't save account information, use it only for order creation
             */
            //$customer->save();
        }
        $this->getQuote()->setCustomer($customer);
        return $this;
    }

    /**
     * Retrieve new customer email
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  string
     */
    protected function _getNewCustomerEmail($customer)
    {
        $email = $this->getData('account/email');
        if (empty($email)) {
            $host = $this->getSession()->getStore()->getConfig(Mage_Customer_Model_Customer::XML_PATH_DEFAULT_EMAIL_DOMAIN);
            $email = $customer->getIncrementId().'@'. $host;
        }
        return $email;
    }
}