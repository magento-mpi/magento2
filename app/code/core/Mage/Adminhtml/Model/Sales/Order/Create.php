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
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Model_Sales_Order_Create
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
    
    protected $_needCollect;
    
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
    
    public function setRecollect($flag)
    {
        $this->_needCollect = $flag;
        return $this;
    }
    
    public function saveQuote()
    {
        if (!$this->getQuote()->getId()) {
            return $this;
        }
        if ($this->_needCollect) {
            $this->getQuote()->collectTotals();
            /*$this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->getQuote()->getShippingAddress()->collectShippingRates();            */
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
                $this->getSession()->getCustomer()
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
        }
        
        return $this->_cart;
    }
    
    /**
     * Move quote item to another items store
     *
     * @param   mixed $item
     * @param   string $mogeTo
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function moveQuoteItem($item, $moveTo)
    {
        if ($item = $this->_getQuoteItem($item)) {
            switch ($moveTo) {
                case 'cart':
                    if ($cart = $this->getCustomerCart()) {
                        $cart->addProduct($item->getProduct());
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
    
    public function removeItem($item, $from)
    {
        switch ($from) {
            case 'quote':
                $this->removeQuoteItem($item);
                break;
            case 'cart':
                if ($cart = $this->getCustomerCart()) {
                    $cart->removeItem($item);
                    $cart->collectTotals()
                        ->save();
                }
                break;
            case 'wishlist':
                if ($wishlist = $this->getCustomerWishlist()) {
                    $item = Mage::getModel('wishlist/item')->load($item);
                    $item->delete();
                }
                break;
            case 'compared':
                
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
            $this->getQuote()->addProduct($product);
            $item = $this->getQuote()->getItemByProduct($product);
            $item->setQty($qty);
        }
        
        $this->setRecollect(true);
        return $this;
    }
    
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $data) {
            $qty = isset($data['qty']) ? (int)$data['qty'] : 1;
        	$this->addProduct($productId, $qty);
        }
        return $this;
    }
    
    public function updateQuoteItems($data)
    {
        if (is_array($data)) {
            foreach ($data as $itemId => $itemQty) {
                $itemQty = (int) $itemQty;
                $itemQty = $itemQty>0 ? $itemQty : 1;
                
            	if ($item = $this->getQuote()->getItemById($itemId)) {
            	    $item->setQty($itemQty);
            	}
            }
            $this->setRecollect(true);
        }
        return $this;
    }
    
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }
    
    public function setShippingAddress($address)
    {
        if (is_array($address)) {
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
    
    public function getBillingAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }
    
    public function setBillingAddress($address)
    {
        if (is_array($address)) {
            $billingAddress = Mage::getModel('sales/quote_address')
                ->setData($address);
            $billingAddress->implodeStreetAddress();
        }

        if ($this->getShippingAddress()->getSameAsBilling()) {
            $shippingAddress = clone $billingAddress;
            $shippingAddress->setSameAsBilling(true);
            $this->setShippingAddress($address);
        }
        
        $this->getQuote()->setBillingAddress($billingAddress);
        return $this;
    }
    
    public function setShippingMethod($method)
    {
        $this->getShippingAddress()->setShippingMethod($method);
        return $this;
    }
    
    public function setPaymentMethod($method)
    {
        $this->getQuote()->getPayment()->setMethod($method);
        return $this;
    }

    public function createOrder()
    {
        $order = Mage::getModel('sales/order');

        $order->createFromQuoteAddress($this->getQuote()->getShippingAddress());
        $order->setStoreId($this->getQuote()->getStore()->getId());
        $order->setOrderCurrencyCode($this->getQuote()->getStore()->getCurrentCurrencyCode());
        $order->setInitialStatus();
        $order->validate();
        if ($order->getErrors()) {
            
        }
        $order->save();
        return $order;
    }
    
    /**
     * Parse data retrieved from request
     *
     * @param   array $data
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function importPostData($data)
    {
        if (isset($data['billing_address'])) {
            $data['billing_address']['customer_address_id'] = isset($data['customer_address_id']) ? $data['customer_address_id'] : '';
            $this->setBillingAddress($data['billing_address']);
        }
        
        if (isset($data['shipping_address'])) {
            $data['shipping_address']['customer_address_id'] = isset($data['customer_address_id']) ? $data['customer_address_id'] : '';
            $this->setShippingAddress($data['shipping_address']);
        }
        
        if (isset($data['shipping_method'])) {
            $this->setShippingMethod($data['shipping_method']);
        }
        
        if (isset($data['payment_method'])) {
            $this->setPaymentMethod($data['payment_method']);
        }
        
        if (isset($data['items'])) {
            
        }
        return $this;
    }
}
