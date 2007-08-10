<?php
/**
 * Multishipping checkout model
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Model_Type_Multishipping extends Mage_Checkout_Model_Type_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }
    
    protected function _init()
    {
        /**
         * reset quote shipping addresses and items
         */
        $this->getQuote()->setIsMultiShipping(true);
        if ($this->getCheckoutSession()->getCheckoutState() === Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN) {
            $this->getCheckoutSession()->setCheckoutState(true);
            $addresses  = $this->getQuote()->getAllShippingAddresses();
            foreach ($addresses as $address) {
            	$this->getQuote()->removeAddress($address->getId());
            }
            
            if ($defaultShipping = $this->getCustomerDefaultShippingAddress()) {
                $quoteShippingAddress = $this->getQuote()->getShippingAddress();
                $quoteShippingAddress->importCustomerAddress($defaultShipping);
                
                foreach ($this->getQuoteItems() as $item) {
                    $addressItem = Mage::getModel('sales/quote_address_item')
                        ->importQuoteItem($item);
                        
                	$quoteShippingAddress->addItem($addressItem);
                }
            }
            if ($this->getCustomerDefaultBillingAddress()) {
                $this->getQuote()->getBillingAddress()
                    ->importCustomerAddress($this->getCustomerDefaultBillingAddress());
            }
            
            $this->getQuote()->save();
        }
    }
    
    public function getQuoteShippingAddressesItems()
    {
        $items = array();
        $addresses  = $this->getQuote()->getAllShippingAddresses();
        foreach ($addresses as $address) {
        	foreach ($address->getAllItems() as $item) {
        		for ($i=0;$i<$item->getQty();$i++){
        		    $addressItem = clone $item;
        		    $addressItem->setQty(1)
                        ->setCustomerAddressId($address->getCustomerAddressId());
        		    $items[] = $addressItem;
        		}
        	}
        }
        return $items;
    }
    
    public function removeAddressItem($addressId, $itemId)
    {
        $address = $this->getQuote()->getAddressById($addressId);
        if ($address) {
            if ($item = $address->getItemById($itemId)) {
                if ($item->getQty()>1) {
                    $item->setQty($item->getQty()-1);
                }
                else {
                    $address->removeItem($item->getId());
                }
                
                if ($quoteItem = $this->getQuote()->getItemById($item->getQuoteItemId())) {
                    $quoteItem->setQty($quoteItem->getQty()-1);
                }
                
                $this->getQuote()->save();
            }
        }
        return $this;
    }
    
    public function setShippingItemsInformation($info)
    {
        if (is_array($info)) {
            $addresses  = $this->getQuote()->getAllShippingAddresses();
            foreach ($addresses as $address) {
            	$this->getQuote()->removeAddress($address->getId());
            }
            
            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                	$this->_addShippingItem($quoteItemId, $data);
                }
            }
            $addresses  = $this->getQuote()->getAllShippingAddresses();
            foreach ($addresses as $address) {
            	$address->collectTotals();
            }

            $this->getQuote()->save();
        }
        return $this;
    }
    
    protected function _addShippingItem($quoteItemId, $data)
    {
    	$qty       = isset($data['qty']) ? (int) $data['qty'] : 0;
    	$addressId = isset($data['address']) ? (int) $data['address'] : false;
    	$quoteItem = $this->getQuote()->getItemById($quoteItemId);
    	
    	if ($addressId && $qty > 0) {
    	    $quoteItem->setMultisippingQty((int)$quoteItem->getMultisippingQty()+$qty);
    	    $quoteItem->setQty($quoteItem->getMultisippingQty());
    	    
    	    $address = $this->getCustomer()->getAddressById($addressId);
    	    if ($address) {
    	        if (!$quoteAddress = $this->getQuote()->getShippingAddressByCustomerAddressId($addressId)) {
        	        $quoteAddress = Mage::getModel('sales/quote_address')
        	           ->importCustomerAddress($address);
                    $this->getQuote()->addShippingAddress($quoteAddress);
    	        }
                
    	        $quoteAddress = $this->getQuote()->getShippingAddressByCustomerAddressId($address->getId());
                
    	        if ($quoteAddressItem = $quoteAddress->getItemByQuoteItemId($quoteItemId)) {
    	            $quoteAddressItem->setQty($quoteAddressItem->getQty()+$qty);
    	        }
    	        else {
                    $quoteAddressItem = Mage::getModel('sales/quote_address_item')
                        ->importQuoteItem($quoteItem)
                        ->setQty($qty);
                    $quoteAddress->addItem($quoteAddressItem);                        
    	        }
    	    }
    	}
        return $this;
    }
    
    public function updateQuoteCustomerShippingAddress($addressId)
    {
        if ($address = $this->getCustomer()->getAddressById($addressId)) {
            $this->getQuote()->getShippingAddressByCustomerAddressId($addressId)
                ->importCustomerAddress($address)
                ->collectTotals();
            $this->getQuote()->save();
        }
        return $this;
    }
    
    public function setQuoteCustomerBillingAddress($addressId)
    {
        if ($address = $this->getCustomer()->getAddressById($addressId)) {
            $this->getQuote()->getBillingAddress($addressId)
                ->importCustomerAddress($address)
                ->collectTotals();
            $this->getQuote()->save();
        }
        return $this;
    }
    
    public function setShippingMethods($methods)
    {
        $addresses = $this->getQuote()->getAllShippingAddresses();
        foreach ($addresses as $address) {
        	if (isset($methods[$address->getId()])) {
        	    $address->setShippingMethod($methods[$address->getId()]);
        	}
        	elseif (!$address->getShippingMethod()) {
        	    Mage::throwException('Address shipping method do not defined');
        	}
        }
        $addresses = $this->getQuote()->save();
        return $this;
    }
    
    public function setPaymentMethod($payment)
    {
        if (!isset($payment['method'])) {
            Mage::throwException('Payment method do not defined');
        }
        
        $this->getQuote()->getPayment()
            ->importPostData($payment)
            ->save();
        return $this;
    }
}
