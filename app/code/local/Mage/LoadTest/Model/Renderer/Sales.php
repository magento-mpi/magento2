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

    protected $_dates = array();

    /**
     * Quote data fro profiler
     *
     * @var array
     */
    protected $_quote;

    /**
     * Order data fro profiler
     *
     * @var array
     */
    protected $_order;

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
        $this->setYearAgo(2);
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
        $this->_profilerBegin();
        if ($this->getType() == 'ORDER') {
            for ($i = 0; $i < $this->getCountOrders(); $i++) {
                if (!$this->_checkMemorySuffice()) {
                    $urlParams = array(
                        'count_orders='.($this->getCountOrders() - $i),
                        'payment_method='.$this->getPaymentMethod(),
                        'shipping_method='.$this->getShippingMethod(),
                        'min_products='.$this->setMinProducts(),
                        'max_products='.$this->setMaxProducts(),
                        'year_ago='.$this->getYearAgo(),
                        'detail_log='.$this->getDetailLog()
                    );
                    $this->_urls[] = Mage::getUrl('*/*/*/') . ' GET:"'.join(';', $urlParams).'"';
                    break;
                }
                $this->_createOrder();
            }
        }
        elseif ($this->getType() == 'QUOTE') {
            for ($i = 0; $i < $this->getCountQuotes(); $i++) {
                if (!$this->_checkMemorySuffice()) {
                    $urlParams = array(
                        'count_quotes='.($this->getCountQuotes() - $i),
                        'payment_method='.$this->getPaymentMethod(),
                        'shipping_method='.$this->getShippingMethod(),
                        'min_products='.$this->setMinProducts(),
                        'max_products='.$this->setMaxProducts(),
                        'year_ago='.$this->getYearAgo(),
                        'detail_log='.$this->getDetailLog()
                    );
                    $this->_urls[] = Mage::getUrl('*/*/*/') . ' GET:"'.join(';', $urlParams).'"';
                    break;
                }
                $this->_createQuote();
            }
        }
        $this->_profilerEnd();
        return $this;
    }

    /**
     * Delete all Quotes/Orders
     *
     * @return Mage_LoadTest_Model_Renderer_Sales
     */
    public function delete()
    {
        $this->_profilerBegin();
        $this->_loadData(false);

        if ($this->getType() == 'ORDER') {
            $collection = Mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToSelect('customer_id')
                ->load();
            foreach ($collection as $order) {
                $this->_profilerOperationStart();
                $customer = $this->_customers[$order->getCustomerId()];
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
                $this->_order = array(
                    'id'            => $order->getId(),
                    'customer_id'   => $order->getCustomerId(),
                    'customer_name' => $customerName,
                );
                $order->delete();
                $this->_profilerOperationStop();
            }
        }
        else {
            $collection = Mage::getModel('sales/quote')
                ->getCollection()
                ->addAttributeToSelect('customer_id')
                ->load();
            foreach ($collection as $quote) {
                /* @var $quote Mage_Sales_Model_Quote */
                $this->_profilerOperationStart();
                $customerId = $quote->getCustomerId();
                $customerName = $this->_customers[$customerId]->getFirstname() . ' ' . $this->_customers[$customerId]->getLastname();
                $this->_quote = array(
                    'id'            => $quote->getId(),
                    'customer_id'   => $customerId,
                    'customer_name' => $customerName,
                );
                $quote->delete();
                $this->_profilerOperationStop();
            }
        }

        $this->_profilerEnd();
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
        $this->_loadData();
        if (!$returnObject) {
            $this->_profilerOperationStart();
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

        $this->_quote = array(
            'id'            => $quoteId,
            'customer_id'   => $customer->getId(),
            'customer_name' => $customerName,
        );

        unset($customer);

        if ($returnObject) {
            return $quote;
        }
        else {
            unset($quote);
            $this->_profilerOperationStop();
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
        $this->_loadData();
        $this->_profilerOperationStart();
        $quote = $this->_createQuote(true);
        /* @var $quote Mage_Sales_Model_Quote */
        $quoteConvert = Mage::getModel('sales/convert_quote');
        /* @var $quoteConvert Mage_Sales_Model_Convert_Quote */

        $order = $quoteConvert->addressToOrder($quote->getShippingAddress());
        /* @var $order Mage_Sales_Model_Order */
        $order->setBillingAddress($quoteConvert->addressToOrderAddress($quote->getBillingAddress()))
            ->setShippingAddress($quoteConvert->addressToOrderAddress($quote->getShippingAddress()))
            ->setPayment($quoteConvert->paymentToOrderPayment($quote->getPayment()))
            ->setCreatedAt($this->_getRandomDate());

        foreach ($quote->getShippingAddress()->getAllItems() as $item) {
            $order->addItem($quoteConvert->itemToOrderItem($item));
        }

        try {
            $order->place()
                ->save();
        }
        catch (Exception $e) {
            Mage::throwException($e->__toString() . "\n\n" . print_r($order->getData(), true));
        }

        $orderId = $order->getId();
        $this->_order = $this->_quote;
        $this->_order['id'] = $orderId;


        unset($quote);
        unset($quoteConvert);
        unset($order);

        $this->_profilerOperationStop();

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
            if ($customer->getQuote()) {
                return $this->_getCustomer($noQuote);
            }
            else {
                $customer->setQuote(true);
            }
        }

        return clone $customer;
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
            $_product = $this->_products[$itemId];
            /* @var $_product Mage_Catalog_Model_Product */
            if ($max = $_product->getStockItem()->getQty()) {
                $qty = rand(1, $max);
            }
            else {
                $qty = 1;
            }
            $quote->addProduct(clone $this->_products[$itemId], $qty);
        }
    }

    protected function _getRandomDate()
    {
        if (empty($this->_dates[$this->getYearAgo()])) {
            $this->_dates[$this->getYearAgo()] = mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y') - $this->getYearAgo());
        }
        return date('Y-m-d H:i:s', rand($this->_dates[$this->getYearAgo()], time()));
    }

    /**
     * Load model data
     *
     */
    protected function _loadData($exception = true)
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
                        ->loadByCustomer($customer);
                    $customer->setQuote($quotes->getEntityId() ? true : false);
                    $noQuote += $quotes->getEntityId() ? 0 : 1;
                }
            }
            unset($collection);

            if ($exception && count($this->_customers) == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('Customers not found, please create customer(s) first'));
            }
            if ($exception && $this->getType() == 'QUOTE' && $noQuote == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('All active customers already have active quotes'));
            }
            if ($exception && $this->getType() == 'QUOTE' && $this->getCountQuotes() > $noQuote) {
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

    protected function _profilerOperationStop()
    {
        parent::_profilerOperationStop();

        if ($this->getDebug()) {
            if ($this->getType() == 'ORDER') {
                if (!$this->_xmlFieldSet) {
                    $this->_xmlFieldSet = $this->_xmlResponse->addChild('orders');
                }

                $order = $this->_xmlFieldSet->addChild('order');
                $order->addAttribute('id', $this->_order['id']);
                $order->addChild('customer', $this->_order['customer_name'])
                    ->addAttribute('id', $this->_order['customer_id']);
                $this->_profilerOperationAddDebugInfo($order);
            }
            elseif ($this->getType() == 'QUOTE') {
                if (!$this->_xmlFieldSet) {
                    $this->_xmlFieldSet = $this->_xmlResponse->addChild('quotes');
                }

                $quote = $this->_xmlFieldSet->addChild('quote');
                $quote->addAttribute('id', $this->_quote['id']);
                $quote->addChild('customer', $this->_quote['customer_name'])
                    ->addAttribute('id', $this->_quote['customer_id']);
                $this->_profilerOperationAddDebugInfo($quote);
            }
        }
    }
}