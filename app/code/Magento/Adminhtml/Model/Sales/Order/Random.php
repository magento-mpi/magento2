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
 * Create random order
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Model_Sales_Order_Random
{
    /**
     * Quote model object
     *
     * @var Magento_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Order model object
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_order;
    protected $_store;
    protected $_customer;
    protected $_productCollection;

    protected static $_storeCollection;
    protected static $_customerCollection;

    public function __construct()
    {
        $this->_quote = Mage::getModel('Magento_Sales_Model_Quote')->save();
        $this->_order = Mage::getModel('Magento_Sales_Model_Order');
    }

    protected function _getStores()
    {
        if (!self::$_storeCollection) {
            self::$_storeCollection = Mage::getResourceModel('Magento_Core_Model_Resource_Store_Collection')
                ->load();
        }
        return self::$_storeCollection->getItems();
    }

    protected function _getCustomers()
    {
        if (!self::$_customerCollection) {
            self::$_customerCollection = Mage::getResourceModel('Magento_Customer_Model_Resource_Customer_Collection')
                ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'inner')
                ->joinAttribute('shipping_country_id', 'customer_address/country_id', 'default_shipping', null, 'inner')
                ->load();
        }
        return self::$_customerCollection->getItems();
    }

    protected function _getProducts()
    {
        if (!$this->_productCollection) {
            $this->_productCollection= Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Collection');
            //$this->_productCollection->getEntity()->setStore($this->_getStore());
            $this->_productCollection->addAttributeToSelect('name')
                ->addAttributeToSelect('sku')
                ->addAttributeToFilter('type_id', Magento_Catalog_Model_Product_Type::TYPE_SIMPLE)
                ->setVisibility(Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInSearchIds())
                ->load();
        }
        return $this->_productCollection->getItems();
    }

    /**
     * Retrieve customer model
     *
     * @return Magento_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        if (!$this->_customer) {
            $items = $this->_getCustomers();
            $randKey = array_rand($items);
            $this->_customer = $items[$randKey];
        }
        return $this->_customer;
    }

    protected function _getRandomProduct()
    {
        $items = $this->_getProducts();
        $randKey = array_rand($items);
        return isset($items[$randKey]) ? $items[$randKey] : false;
    }

    protected function _getStore()
    {
        if (!$this->_store) {
            $items = $this->_getStores();
            $randKey = array_rand($items);
            $this->_store = $items[$randKey];
        }
        return $this->_store;
    }

    public function render()
    {
        $customer = $this->_getCustomer();
        $this->_quote->setStore($this->_getStore())
            ->setCustomer($customer);
        $this->_quote->getBillingAddress()->importCustomerAddress($customer->getDefaultBillingAddress());
        $this->_quote->getShippingAddress()->importCustomerAddress($customer->getDefaultShippingAddress());

        $productCount = rand(3, 10);
        for ($i=0; $i<$productCount; $i++){
            $product = $this->_getRandomProduct();
            if ($product) {
                $product->setQuoteQty(1);
                $this->_quote->addCatalogProduct($product);
            }
        }
        $this->_quote->getPayment()->setMethod('checkmo');

        $this->_quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');//->collectTotals()->save();
        $this->_quote->getShippingAddress()->setCollectShippingRates(true);
        $this->_quote->collectTotals()
            ->save();
        $this->_quote->save();
        return $this;
    }

    protected function _getRandomDate()
    {
        $timestamp = mktime(rand(0,23), rand(0,59), 0, rand(1,11), rand(1,28), rand(2006, 2007));
        return date('Y-m-d H:i:s', $timestamp);
    }

    public function save()
    {
        $this->_order->setStoreId($this->_getStore()->getId());
        $this->_order->createFromQuoteAddress($this->_quote->getShippingAddress());
        $this->_order->validate();
        $this->_order->setInitialStatus();
        $this->_order->save();
        $this->_order->setCreatedAt($this->_getRandomDate());
        $this->_order->save();

        $this->_quote->setIsActive(false);
        $this->_quote->save();
        return $this;
    }
}
