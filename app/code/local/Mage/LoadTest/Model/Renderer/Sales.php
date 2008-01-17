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
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest Renderer Sales model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_LoadTest_Model_Renderer_Sales extends Mage_LoadTest_Model_Renderer_Abstract
{
    /**
     * Customers collection
     *
     * @var array
     */
    protected $_customers;

    /**
     * Products collection
     *
     * @var array
     */
    protected $_products;

    /**
     * Stores collection
     *
     * @var array
     */
    protected $_stores;

    /**
     * Processed quotes
     *
     * @var array
     */
    public $quotes;

    /**
     * Processed orders
     *
     * @var array
     */
    public $orders;

    /**
     * Init model
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setType('ORDER');
        $this->setPaymentMethod('checkmo');
        $this->setShippingMethod('freeshipping_freeshipping');
        $this->setMinProducts(1);
        $this->setMaxProducts(5);
        $this->setCountQuotes(100);
        $this->setCountOrders(100);
    }

    /**
     * Render Quotes/Orders
     *
     * @return Mage_LoadTest_Model_Renderer_Sales
     */
    public function render()
    {
        if ($this->getType() == 'ORDER') {
            $this->quotes = array();
            $this->orders = array();
            for ($i = 0; $i < $this->getCountOrders(); $i++) {
                $this->_createOrder();
            }
        }
        else {
            $this->quotes = array();
            for ($i = 0; $i < $this->getCountQuotes(); $i++) {
                $this->_createQuote();
            }
        }
        return $this;
    }

    /**
     * Delete all Quotes/Orders
     *
     * @return Mage_LoadTest_Model_Renderer_Sales
     */
    public function delete()
    {
        $this->_loadData();

        if ($this->getType() == 'ORDER') {
            $this->orders = array();
            $collection = Mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToSelect('customer_id')
                ->load();
            foreach ($collection as $order) {
                $this->_beforeUsedMemory();
                $customer = $this->_customers[$order->getCustomerId()];
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
                $this->orders[$order->getId()] = array(
                    'customer_id'   => $order->getCustomerId(),
                    'customer_name' => $customerName,
                );
                $order->delete();
                $this->_afterUsedMemory();
            }
        }
        else {
            $this->quotes = array();
            $collection = Mage::getModel('sales/quote')
                ->getCollection()
                ->addAttributeToSelect('customer_id')
                ->load();
            foreach ($collection as $quote) {
                /* @var $quote Mage_Sales_Model_Quote */
                $this->_beforeUsedMemory();
                $customerId = $quote->getCustomerId();
                $customerName = $this->_customers[$customerId]->getFirstname() . ' ' . $this->_customers[$customerId]->getLastname();
                $this->quotes[$quote->getId()] = array(
                    'customer_id'   => $customerId,
                    'customer_name' => $customerName,
                );
                $quote->delete();
                $this->_afterUsedMemory();
            }
        }
        return $this;
    }

    /**
     * Create Quote
     *
     * @param bool $returnObject
     * @return mixed
     */
    protected function _createQuote($returnObject = false)
    {
        if (!$returnObject) {
            $this->_beforeUsedMemory();
        }
        $quote = Mage::getModel('sales/quote');
        /* @var $quote Mage_Sales_Model_Quote */

        $customer = $this->_getCustomer(empty($returnObject));
        /* @var $customer Mage_Customer_Model_Customer */
        $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
        $store = $this->_stores[$customer->getStoreId()];

        $quote->setStoreId($store->getId());
        $quote->setCustomer($customer);
        $this->_addProductsToQuote($quote);
        $quote->setBillingAddress(Mage::getModel('sales/quote_address')->importCustomerAddress($customer->getDefaultBillingAddress()));
        $quote->setShippingAddress(Mage::getModel('sales/quote_address')->importCustomerAddress($customer->getDefaultShippingAddress()));
        $quote->getPayment()->setMethod($this->getPaymentMethod());
        $quote->getShippingAddress()->setShippingMethod($this->getShippingMethod());
        $quote->collectTotals();
        $quote->save();

        $quoteId = $quote->getId();

        $this->quotes[$quoteId] = array(
            'customer_id'   => $customer->getId(),
            'customer_name' => $customerName,
        );

        if ($returnObject) {
            return $quote;
        }
        else {
            unset($quote);
            $this->_afterUsedMemory();
            return $quoteId;
        }
    }

    /**
     * Create Order
     * Based on quote
     *
     * @return int
     */
    protected function _createOrder()
    {
        $this->_afterUsedMemory();
        $quote = $this->_createQuote(true);
        /* @var $quote Mage_Sales_Model_Quote */
        $quoteConvert = Mage::getModel('sales/convert_quote');
        /* @var $quoteConvert Mage_Sales_Model_Convert_Quote */

        $order = $quoteConvert->addressToOrder($quote->getShippingAddress());
        /* @var $order Mage_Sales_Model_Order */
        $order->setBillingAddress($quoteConvert->addressToOrderAddress($quote->getBillingAddress()))
            ->setShippingAddress($quoteConvert->addressToOrderAddress($quote->getShippingAddress()))
            ->setPayment($quoteConvert->paymentToOrderPayment($quote->getPayment()));

        foreach ($quote->getShippingAddress()->getAllItems() as $item) {
            $order->addItem($quoteConvert->itemToOrderItem($item));
        }

        $order->place()
            ->save();

        $quoteId = $quote->getId();
        $orderId = $order->getId();
        $this->orders[$orderId] = $this->quotes[$quoteId];

        unset($order);

        $this->_beforeUsedMemory();

        return $orderId;
    }

    /**
     * Get customer
     *
     * @param bool $noQuote
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer($noQuote = true)
    {
        $this->_loadData();

        $customer = $this->_customers[array_rand($this->_customers)];
        /* @var $customer Mage_Customer_Model_Customer */

        if ($noQuote) {
            $collection = Mage::getModel('sales/quote')
                ->getCollection()
                ->loadByCustomerId($customer->getId());
            if ($collection) {
                return $this->_getCustomer($noQuote);
            }
        }

        return $customer;
    }

    /**
     * Add product(s) to quote
     *
     * @param Mage_Sales_Model_Quote $quote
     */
    protected function _addProductsToQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->_loadData();

        $itemIds  = array_rand($this->_products, rand($this->getMinProducts(), $this->getMaxProducts()));
        if (is_numeric($itemIds)) {
            $itemIds = array($itemIds);
        }
        foreach ($itemIds as $itemId) {
            $product = $this->_products[$itemId];
            $qty     = rand(1, $product->getStockItem()->getMaxSaleQty());

            $quote->addProduct($product, $qty);
        }
    }

    /**
     * Load model data
     *
     */
    protected function _loadData()
    {
        if (is_null($this->_customers)) {
            $collection = Mage::getModel('customer/customer')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->load();
            $this->_customers = array();
            $noQuote = 0;
            foreach ($collection as $customer) {
                $this->_customers[$customer->getId()] = $customer;
                if ($this->getType() == 'QUOTE') {
                    $quotes = Mage::getModel('sales/quote')
                        ->getCollection()
                        ->loadByCustomerId($customer->getId());
                    $customer->setQuote($quotes ? true : false);
                    $noQuote += $quotes ? 0 : 1;
                }
            }
            unset($collection);

            if (count($this->_customers) == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('Customers not found, please create customer(s) first'));
            }
            if ($this->getType() == 'QUOTE' && $noQuote == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('All active customers already have active quotes'));
            }
            if ($this->getType() == 'QUOTE' && $this->getCountQuotes() > $noQuote) {
                $this->setCountQuotes($noQuote);
            }
        }
        if (is_null($this->_products)) {
            $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->load();
            $this->_products = array();
            foreach ($collection as $product) {
                $this->_products[$product->getId()] = $product;
            }
            unset($collection);

            if (count($this->_products) == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('Products not found, please create product(s) first'));
            }
        }
        if (is_null($this->_stores)) {
            $this->_stores = array();
            $collection = Mage::getModel('core/store')
                ->getCollection();
            foreach ($collection as $item) {
                $this->_stores[$item->getId()] = $item;
            }
            unset($collection);
        }
    }
}