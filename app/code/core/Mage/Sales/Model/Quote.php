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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Sales_Model_Quote extends Mage_Core_Model_Abstract
{
    protected $_customer;
    protected $_addresses;
    protected $_items;
    protected $_payments;

    function _construct()
    {
        $this->_init('sales/quote');
    }

    /**
     * Init new quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function initNewQuote()
    {
        #$this->setBillingAddress(Mage::getModel('sales/quote_address'));
        #$this->setShippingAddress(Mage::getModel('sales/quote_address'));
        return $this;
    }
    
    /**
     * Initialize quote data by customer 
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Sales_Model_Quote
     */
    public function initByCustomer(Mage_Customer_Model_Customer $customer)
    {
        if ($customer->getId()) {
            $this->setCustomer($customer);
            
            $defaultBillingAddress = $customer->getDefaultBillingAddress();
            if ($defaultBillingAddress && $defaultBillingAddress->getId()) {
                $billingAddress = Mage::getModel('sales/quote_address')
                    ->importCustomerAddress($defaultBillingAddress);
                $this->setBillingAddress($billingAddress);
            }
            
            $defaultShippingAddress= $customer->getDefaultShippingAddress();
            if ($defaultShippingAddress && $defaultShippingAddress->getId()) {
                $shippingAddress = Mage::getModel('sales/quote_address')
                    ->importCustomerAddress($defaultShippingAddress);
                $this->setShippingAddress($shippingAddress);
            }
        }
        return $this;
    }

    /**
     * Entity resource
     *
     * dummy function for correct zend studio autocompletion
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getResource()
    {
        return parent::getResource();
    }

    public function toArray(array $arrAttributes = array())
    {
        $arr = parent::toArray($arrAttributes);
        $arr['addresses'] = $this->getAddressesCollection()->toArray();
        $arr['items'] = $this->getItemsCollection()->toArray();
        $arr['payments'] = $this->getPaymentsCollection()->toArray();
        return $arr;
    }

    protected function _beforeSave()
    {
        $baseCurrency  = Mage::app()->getBaseCurrencyCode();
        $storeCurrency = $this->getStore()->getBaseCurrencyCode();
        $quoteCurrency = $this->getStore()->getCurrentCurrencyCode();

        $currency = Mage::getModel('directory/currency');

        $this->setBaseCurrencyCode($baseCurrency);
        $this->setStoreCurrencyCode($storeCurrency);
        $this->setQuoteCurrencyCode($quoteCurrency);
        $this->setStoreToBaseRate($currency->getResource()->getRate($storeCurrency, $baseCurrency));
        $this->setStoreToQuoteRate($currency->getResource()->getRate($storeCurrency, $quoteCurrency));

        Mage::dispatchEvent('sales_quote_save_before', array('quote'=>$this));
        parent::_beforeSave();
    }

/*********************** CUSTOMER ***************************/

    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        $this->setCustomerId($customer->getId());
        $this->setCustomerTaxClassId($customer->getTaxClassId());
        return $this;
    }

    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $customer = Mage::getModel('customer/customer');
            /* @var $customer Mage_Customer_Model_Customer */
            if ($customerId = $this->getCustomerId()) {
                $customer->load($customerId);
                if (! $customer->getId()) {
                    $this->setCustomerId(null);
                }
            }
            $this->_customer = $customer;
        }
        return $this->_customer;
    }

/*********************** ADDRESSES ***************************/

    public function getAddressesCollection()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = Mage::getResourceModel('sales/quote_address_collection');

            if ($this->getId()) {
                $this->_addresses
                    ->addAttributeToSelect('*')
                    ->setQuoteFilter($this->getId())
                    ->load();
                foreach ($this->_addresses as $address) {
                    $address->setQuote($this);
                }
            }
        }
        return $this->_addresses;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='billing' && !$address->isDeleted()) {
                return $address;
            }
        }
        $address = Mage::getModel('sales/quote_address')->setAddressType('billing');
        $this->addAddress($address);
        return $address;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='shipping' && !$address->isDeleted()) {
                return $address;
            }
        }
        $address = Mage::getModel('sales/quote_address')->setAddressType('shipping');
        $this->addAddress($address);
        return $address;
    }

    public function getAllShippingAddresses()
    {
        $addresses = array();
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='shipping' && !$address->isDeleted()) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }
    public function getAllAddresses()
    {
        $addresses = array();
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted()) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    public function getAddressById($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function getAddressByCustomerAddressId($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted() && $address->getCustomerAddressId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function getShippingAddressByCustomerAddressId($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if (!$address->isDeleted() && $address->getAddressType()=='shipping' && $address->getCustomerAddressId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function removeAddress($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                $address->isDeleted(true);
                break;
            }
        }
        return $this;
    }

    public function removeAllAddresses()
    {
        foreach ($this->getAddressesCollection() as $address) {
            $address->isDeleted(true);
        }
        return $this;
    }

    public function addAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setQuote($this)->setParentId($this->getId());
        if (!$address->getId()) {
            $this->getAddressesCollection()->addItem($address);
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote
     */
    public function setBillingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $old = $this->getBillingAddress();

        if (!empty($old)) {
            $old->addData($address->getData());
        } else {
            $this->addAddress($address->setAddressType('billing'));
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote
     */
    public function setShippingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        if ($this->getIsMultiShipping()) {
            $this->addAddress($address->setAddressType('shipping'));
        }
        else {
            $old = $this->getShippingAddress();

            if (!empty($old)) {
                $old->addData($address->getData());
            } else {
                $this->addAddress($address->setAddressType('shipping'));
            }
        }
        return $this;
    }

    public function addShippingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->setShippingAddress($address);
        return $this;
    }

/*********************** ITEMS ***************************/

    public function getItemsCollection($loaded = true)
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/quote_item_collection');

            if ($this->getId()) {
                $this->_items->addAttributeToSelect('*')
                    ->setQuoteFilter($this->getId());
                if ($loaded) {
                    $this->_items->load();
                }
                foreach ($this->_items as $item) {
                    $item->setQuote($this);
                }
            }
            else {
                $this->_items->setQuoteFilter(false);
            }
        }
        return $this->_items;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    public function hasItems()
    {
        return sizeof($this->getAllItems())>0;
    }

    public function hasItemsWithDecimalQty()
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->getProduct()->getQtyIsDecimal()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Enter description here...
     *
     * @param int $itemId
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }

    public function removeItem($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                $item->isDeleted(true);
                break;
            }
        }
        return $this;
    }

    public function addItem(Mage_Sales_Model_Quote_Item $item)
    {
        $item->setQuote($this)
            ->setParentId($this->getId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    public function addCatalogProduct(Mage_Catalog_Model_Product $product)
    {
        if (!$product->getQuoteQty()) {
            $product->setQuoteQty(1);
        }

        $item = $this->getItemByProduct($product);
        if (!$item) {
            $item = Mage::getModel('sales/quote_item');
        }
        $item->importCatalogProduct($product);

        $this->addItem($item);

        return $item;
    }

    /**
     * Retrieve quote item by product id
     *
     * @param   int $productId
     * @return  Mage_Sales_Model_Quote_Item || false
     */
    public function getItemByProduct(Mage_Catalog_Model_Product $product)
    {
        foreach ($this->getAllItems() as $item) {
            if ($item->getSuperProductId()) {
                if ($product->getSuperProduct() && $item->getSuperProductId() == $product->getSuperProduct()->getId()) {
                    if ($item->getProductId() == $product->getId()) {
                        return $item;
                    }
                }
            }
            else {
                if ($item->getProductId() == $product->getId()) {
                    return $item;
                }
            }
        }
        return false;
    }

    public function getItemsSummaryQty()
    {
        $qty = $this->getData('all_items_qty');
        if (is_null($qty)) {
#            $qty = Mage::getResourceModel('sales_entity/quote')->fetchItemsSummaryQty($this);
            $qty = 0;
            foreach ($this->getAllItems() as $item) {
                $qty+= $item->getQty();
            }
            $this->setData('all_items_qty', $qty);
        }
        return $qty;
    }
    
    /**
     * Adding product to quote
     *
     * @param   mixed $product
     * @return  Mage_Sales_Model_Quote
     */
    public function addProduct($product)
    {
        if (is_int($product)) {
            $product = Mage::getModel('catalog/product')
                ->setStore($this->getStore())
                ->load($product);
        }
        
        if ($product instanceof Mage_Catalog_Model_Product) {
            $this->addCatalogProduct($product);
        }
        
        return $this;
    }
/*********************** PAYMENTS ***************************/

    public function getPaymentsCollection()
    {
        if (is_null($this->_payments)) {
            $this->_payments = Mage::getResourceModel('sales/quote_payment_collection');

            if ($this->getId()) {
                $this->_payments
                    ->addAttributeToSelect('*')
                    ->setQuoteFilter($this->getId())
                    ->load();
                foreach ($this->_payments as $payment) {
                    $payment->setQuote($this);
                }
            }
        }
        return $this->_payments;
    }


    public function getPayment()
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                return $payment;
            }
        }
        $address = Mage::getModel('sales/quote_payment');
        $this->addPayment($address);
        return $address;
    }

    public function getPaymentById($paymentId)
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if ($payment->getId()==$paymentId) {
                return $payment;
            }
        }
        return false;
    }

    public function addPayment(Mage_Sales_Model_Quote_Payment $payment)
    {
        $payment->setQuote($this)->setParentId($this->getId());
        if (!$payment->getId()) {
            $this->getPaymentsCollection()->addItem($payment);
        }
        return $this;
    }

    public function setPayment(Mage_Sales_Model_Quote_Payment $payment)
    {
        if (!$this->getIsMultiPayment() && ($old = $this->getPayment())) {
            $payment->setId($old->getId());
        }
        $this->addPayment($payment);

        return $payment;
    }

    public function removePayment()
    {
        $this->getPayment()->isDeleted(true);
        return $this;
    }

/*********************** TOTALS ***************************/
    public function collectTotals()
    {
        $this->setGrandTotal(0);
        foreach ($this->getAllShippingAddresses() as $address) {
            $address->collectTotals();
            $this->setGrandTotal((float) $this->getGrandTotal()+$address->getGrandTotal());
        }
        return $this;
    }

    public function getTotals()
    {
        return $this->getShippingAddress()->getTotals();
    }

/*********************** ORDER ***************************/

    public function createOrder()
    {
        if ($this->getIsVirtual()) {
            $this->getBillingAddress()->createOrder();
        } elseif (!$this->getIsMultiShipping()) {
            $this->getShippingAddress()->createOrder();
        } else {
            foreach ($this->getAllShippingAddresses() as $address) {
                $address->createOrder();
            }
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            if ($storeId = $this->getStoreId()) {
                $store = Mage::getModel('core/store')->load($this->getStoreId());
                /* @var $store Mage_Core_Model_Store */
                if ($store->getId()) {
                    $this->setStore($store);
                }
            }
            if (is_null($this->_store)) {
                $this->setStore(Mage::app()->getStore());
            }
        }
        return $this->_store;
    }

    /**
     * Set store
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Sales_Model_Quote
     */
    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->_store = $store;
        $this->setStoreId($store->getId());
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param int $storeId
     * @return Mage_Sales_Model_Quote
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
        if (! is_null($this->_store) && ($this->_store->getId() != $storeId)) {
            $this->_store = null;
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Quote
     */
    public function createFromOrder(Mage_Sales_Model_Order $order)
    {
        $this->setStoreId($order->getStoreId());
        $this->setBillingAddress(Mage::getModel('sales/quote_address')->importOrderAddress($order->getBillingAddress()));
        $this->setShippingAddress(Mage::getModel('sales/quote_address')->importOrderAddress($order->getShippingAddress()));
        foreach ($order->getItemsCollection() as $item) {
            if ($item->getQtyToShip() > 0) {
                $this->addItem(Mage::getModel('sales/quote_item')->importOrderItem($item));
            }
        }
        $this->setCouponCode($order->getCouponeCode());
        $this->setShippingMethod($order->getShippingMethod());
        return $this;
    }
    
    /**
     * Loading quote data by customer
     *
     * @return mixed
     */
    public function loadByCustomer($customer)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customerId = $customer->getId();
        }
        else {
            $customerId = (int) $customer;
        }
        $this->getResource()->loadByCustomerId($this, $customerId);
        return $this;
    }
}