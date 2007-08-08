<?php
/**
 * Multishipping checkout choose item addresses block
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Block_Multishipping_Addresses extends Mage_Core_Block_Template
{
    public function getItems()
    {
        $items = Mage::getSingleton('checkout/type_multishipping')->getQuoteSplittedItems();
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }
    
    public function getShippingAddressesSelect($item)
    {
        
    }
    
    public function getItemUrl($item)
    {
        return $this->getUrl('catalog/product/view/id/'.$item->getProductId());
    }
    
    public function getItemDeleteUrl()
    {
        
    }
    
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/addressesPost');
    }
    
    public function getNewAddressUrl()
    {
        
    }
    
    public function getBackUrl()
    {
        return Mage::getUrl('*/cart/');
    }
}
