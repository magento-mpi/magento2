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
    public function getQuoteAddressItem()
    {
        
    }
    /**
     * Retrieve quote items splitted by qty and shipping address
     *
     * @return array
     */
    public function getQuoteSplittedItems()
    {
        $res = array();
        
        $items = $this->getQuoteItems();
        foreach ($items as $item) {
        	for ($i=0;$i<$item->getQty();$i++){
        	    $res[] = $this->_getSplittedItem($item, $i);
        	}
        }
        return $res;
    }
    
    /**
     * Retrieve slitted part of base order item
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @param   int $index
     * @return  Mage_Sales_Model_Quote_Item
     */
    protected function _getSplittedItem($item, $index)
    {
        $spitedItem = clone $item;
        $spitedItem->setQty(1);
        
        if ($shippingAddresses = $item->getShippingAddresses()) {
            $shippingAddresses = explode(',', $shippingAddresses);
        }
        else {
            $shippingAddresses = array();
        }
        
        if (isset($shippingAddresses[$index])) {
            $spitedItem->setShippingAddressId($shippingAddresses[$index]);
        }
        else {
            if ($address = $this->getCustomerDefaultShippingAddress()) {
                $spitedItem->setShippingAddressId($address->getId());
            }
        }
        return $spitedItem;
    }
    
    public function removeQuoteItem()
    {
        
    }
}
