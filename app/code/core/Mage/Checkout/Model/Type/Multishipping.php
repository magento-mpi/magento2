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
                        ->setData($item->getData());
                	$quoteShippingAddress->addItem($addressItem);
                }
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
        		    echo '<pre>';
        		    print_r($addressItem->getData());
        		    echo '</pre>';
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
        //var_dump($address);
        if ($address) {
            $item = $address->getItemById($itemId);
            /*echo '<pre>';
            print_r($item->getData());
            echo '</pre>';
            die();*/
        }
        return $this;
    }
}
