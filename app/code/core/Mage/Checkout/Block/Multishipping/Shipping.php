<?php
/**
 * Mustishipping checkout shipping
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Block_Multishipping_Shipping extends Mage_Checkout_Block_Multishipping_Abstract
{
    public function getAddresses()
    {
        return $this->getCheckout()->getQuote()->getAllShippingAddresses();
    }
    
    public function getAddressCount()
    {
        $count = $this->getData('address_count');
        if (is_null($count)) {
            $count = count($this->getAddresses());
            $this->setData('address_count', $count);
        }
        return $count;
    }
    
    public function getAddressItems($address)
    {
        $items = $address->getAllItems();
        foreach ($items as $item) {
        	$item->setQuoteItem($this->getCheckout()->getQuote()->getItemById($item->getQuoteItemId()));
        }
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);        
    }
    
    public function getShippingMethods($address)
    {
        $items = $address->getAllShippingRates();
        echo '<pre>';
        print_r($items);
        echo '</pre>';
    }
    
    public function getAddressEditUrl($address)
    {
        return $this->getUrl('*/multishipping_address/editShipping', array('id'=>$address->getCustomerAddressId()));
    }
    
    public function getItemsEditUrl()
    {
        
    }
    
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/shippingPost');
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/addresses');
    }
}
