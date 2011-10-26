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
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest Renderer Sales model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
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

    private $_orderProducts = array();

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
                $customer = $this->_getCustomerItem($order->getCustomerId());
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
		$customer = $this->_getCustomerItem($customerId);
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
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
	$quote->setIsSuperMode(true);
        /* @var $quote Mage_Sales_Model_Quote */
	$this->_quote = array();

        $this->_addProductsToQuote($quote);

	$customer = $this->_getCustomer(empty($returnObject));
        /* @var $customer Mage_Customer_Model_Customer */
        $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
        $store = $this->_getStore($customer->getStoreId());
        $quote->setStoreId($store->getId());
        $quote->setCustomer($customer);

	foreach($customer->getAddressesCollection() as $address) {
	    $quoteAddress = Mage::getModel('sales/quote_address')->importCustomerAddress($address);
	    if($address->getIsPrimaryBilling())
		$quote->setBillingAddress($quoteAddress);
	    if($address->getIsPrimaryShipping())
		$quote->setShippingAddress($quoteAddress);

	}

	$paymentMethod = $this->getPaymentMethod();
	$shippingMethod = $this->getShippingMethod();

	$quote->getPayment()->setMethod($this->getPaymentMethod());
	$quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->getShippingAddress()
	    ->setShippingMethod($shippingMethod);
	$this->_quote['address_count'] = count($quote->getAllAddresses());
	$this->_quote['visible_count'] = count($quote->getAllVisibleItems());
		Magento_Profiler::reset("quote::payment::total");
		Magento_Profiler::reset("quote::total::foreach");
		Magento_Profiler::reset("quote::total::foreachitems");
	Magento_Profiler::start("quote::payment::total");
        $quote->collectTotals();
	Magento_Profiler::stop("quote::payment::total");

        $quote->save();

        $quoteId = $quote->getId();
        $this->_quote = array_merge($this->_quote,
            array('id'		  => $quoteId,
		  'customer_id'   => $customer->getId(),
		  'customer_name' => $customerName,)
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
	Magento_Profiler::start("quote::create");
        $quote = $this->_createQuote(true);
	Magento_Profiler::stop("quote::create");

	Magento_Profiler::start("order::save");
        $service = Mage::getModel('sales/service_quote', $quote);
        $order = $service->submit();

        /* @var $quote Mage_Sales_Model_Quote */
        //$quoteConvert = Mage::getModel('sales/convert_quote');
        /* @var $quoteConvert Mage_Sales_Model_Convert_Quote */
	//Magento_Profiler::start("order::addresses::add");
       // $order = $quoteConvert->addressToOrder($quote->getShippingAddress());
        /* @var $order Mage_Sales_Model_Order */
        /*$order->setBillingAddress($quoteConvert->addressToOrderAddress($quote->getBillingAddress()))
            ->setShippingAddress($quoteConvert->addressToOrderAddress($quote->getShippingAddress()))
            ->setPayment($quoteConvert->paymentToOrderPayment($quote->getPayment()))
            ->setCreatedAt($this->_getRandomDate());

        foreach ($quote->getShippingAddress()->getAllItems() as $item) {
            $order->addItem($quoteConvert->itemToOrderItem($item));
        }
	Magento_Profiler::stop("order::addresses::add");
	Magento_Profiler::start("order::save");
        try {
            $order->place()
                ->save();
        }
        catch (Exception $e) {
            Mage::throwException($e->__toString() . "\n\n" . print_r($order->getData(), true));
        }*/
	Magento_Profiler::stop("order::save");
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

        $customer = $this->_getCustomerItem(array_rand($this->_customers));
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
	$this->_order['products'] = array();
//	$itemIds = array(12, 13);
//	$itemIds = array(1, 2, 3, 4, 5);
        $itemIds  = array_rand($this->_products,
		rand($this->getMinProducts(),
		$this->getMaxProducts()));

        if (is_numeric($itemIds)) {
            $itemIds = array($itemIds);
        }
	$request = array();
        foreach ($itemIds as $itemId) {
	    /* @var $_product Mage_Catalog_Model_Product */
            $_product = $this->_getProduct($itemId);
	    $debugInfo = array('id' => $itemId, 'type' => $_product->getTypeId());
	    //$params = array('product' => &$_product);
	    $itemTypeInstance = $this->_getItemTypeInstance($_product->getTypeId());
	    if ($itemTypeInstance) {
		$request = $itemTypeInstance->prepareRequestForCart($_product);
		if($request instanceof Varien_Object) {
		    //Magento_Profiler::start("product::addtocart");
		    $quote->addProduct(clone $_product, $request);
		    //Magento_Profiler::stop("product::addtocart");
		}
		/*$debugInfo['time'] = array(
		  'init_options'    => Magento_Profiler::fetch("option::collection::init"),
		  'insider'         => Magento_Profiler::fetch("option::collection::insider"),
		  'init_selections' => Magento_Profiler::fetch("selection::collection::init"),
		  'foreach'         => Magento_Profiler::fetch("filterd::options::foreach"),
		  'addtocart'       => Magento_Profiler::fetch("product::addtocart"),
		);
		Magento_Profiler::reset("option::collection::init");
		Magento_Profiler::reset("option::collection::insider");
		Magento_Profiler::reset("selection::collection::init");
		Magento_Profiler::reset("filterd::options::foreach");
		Magento_Profiler::reset("product::addtocart");*/
	    }
	    $this->_quote['products'][] = $debugInfo;
        }
    }

    protected function _getItemTypeInstance($type, $params = array())
    {
	return Mage::getSingleton('loadtest/renderer_sales_item_type_' . strtolower($type));
    }

    protected function _getProduct($product_id)
    {
	if(!$this->_products[$product_id] instanceof Varien_Object) {
	    $_product = Mage::getModel('catalog/product')->load($product_id);
	    $this->_products[$product_id] = $_product;
	}
	return $this->_products[$product_id];
    }

    protected function _getStore($store_id)
    {
	if(!$this->_stores[$store_id] instanceof Varien_Object) {
	    $this->_stores[$store_id] = Mage::getModel('core/store')->load($store_id);
	}
	return $this->_stores[$store_id];
    }

    protected function _getCustomerItem($customer_id)
    {
	$noQuote = 0;
	if(!$this->_customers[$customer_id] instanceof Varien_Object) {
	    $_customer = Mage::getModel('customer/customer')->load($customer_id);
            $this->_customers[$customer_id] = $_customer;
            if ($this->getType() == 'QUOTE') {
                $quotes = Mage::getModel('sales/quote')
                    ->loadByCustomer($_customer);
                $_customer->setQuote($quotes->getEntityId() ? true : false);
                $noQuote += $quotes->getEntityId() ? 0 : 1;
            }

	    /*if ($exception && $this->getType() == 'QUOTE' && $noQuote == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('All active customers already have active quotes.'));
            }
            if ($exception && $this->getType() == 'QUOTE' && $this->getCountQuotes() > $noQuote) {
                $this->setCountQuotes($noQuote);
            }*/
	}
	return $this->_customers[$customer_id];
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
        $this->_loadCustomersIds();
        $this->_loadProductsIds();
        $this->_loadStoresIds();
    }

    protected function _loadCustomersIds()
    {
	if (is_null($this->_customers)) {
            /*$customers_ids = Mage::getModel('customer/customer')
                ->getCollection()
                ->addAttributeToSelect('*')
		->getAllIds();*/
                //->load();
	    $customers_ids = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection')
		->getAllIds();
            $this->_customers = array();
            //$noQuote = 0;
            foreach ($customers_ids as $customer_id) {
		$this->_customers[$customer_id] = $customer_id;
		/*$_customer = Mage::getModel('customer/customer')->load($customer_id);
                $this->_customers[$customer_id] = $_customer;
                if ($this->getType() == 'QUOTE') {
                    $quotes = Mage::getModel('sales/quote')
                        ->loadByCustomer($_customer);
                    $_customer->setQuote($quotes->getEntityId() ? true : false);
                    $noQuote += $quotes->getEntityId() ? 0 : 1;
                }*/
            }
            unset($customers_ids);

            /*if ($exception && count($this->_customers) == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('Customers not found, please create customer(s) first.'));
            }
            if ($exception && $this->getType() == 'QUOTE' && $noQuote == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('All active customers already have active quotes.'));
            }
            if ($exception && $this->getType() == 'QUOTE' && $this->getCountQuotes() > $noQuote) {
                $this->setCountQuotes($noQuote);
            }*/
        }
    }

    protected function _loadProductsIds()
    {
	if (is_null($this->_products)) {
            /*$product_ids = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
		->getAllIds();*/
                //->load();
	    $product_ids = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Collection')
		->getAllIds();
            $this->_products = array();

	    foreach ($product_ids as $product_id) {
		//$_product = Mage::getModel('catalog/product')->load($product_id);
                //$this->_products[$product_id] = $_product;
		$this->_products[$product_id] = $product_id;
            }
            unset($product_ids);

            if (count($this->_products) == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('Products not found, please create product(s) first.'));
            }
        }
    }

    protected function _loadStoresIds()
    {
	if (is_null($this->_stores)) {
            $this->_stores = array();
            $stores_ids = Mage::getModel('core/store')
                ->getCollection()
		->getAllIds();
            foreach ($stores_ids as $store_id) {
                //$this->_stores[$store_id] = Mage::getModel('core/store')->load($store_id);
		$this->_stores[$store_id] = $store_id;
            }
            unset($stores_ids);
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

		$order->addChild('quote_total', Magento_Profiler::fetch("quote::payment::total"));
		$order->addChild('quote_total_foreach', Magento_Profiler::fetch("quote::total::foreach"));
		$order->addChild('quote_total_foreach_1', Magento_Profiler::fetch("quote::total::foreach::1"));
		$order->addChild('quote_total_foreach_2', Magento_Profiler::fetch("quote::total::foreach::2"));
		$order->addChild('quote_total_foreachitems', Magento_Profiler::fetch("quote::total::foreachitems"));
		$order->addChild('address_count', $this->_quote['address_count']);
		$order->addChild('visible_count', $this->_quote['visible_count']);



		/*foreach($this->_order['products'] as $product) {
		    $productNode = $order->addChild('product', $product['type']);
		    $productNode->addAttribute('id', $product['id']);
		    foreach($product['time'] as $timer_name => $sum)
			$productNode->addChild($timer_name, $sum);
		}*/
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