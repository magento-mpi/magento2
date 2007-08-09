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
class Mage_Checkout_Block_Multishipping_Addresses extends Mage_Checkout_Block_Multishipping_Abstract
{
    public function getItems()
    {
        $items = $this->getCheckout()->getQuoteShippingAddressesItems();
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }
    
    /**
     * Retrieve HTML for addresses dropdown
     * 
     * @param  $item
     * @return string
     */
    public function getAddressesHtmlSelect($item, $index)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            //->setName('ship['.$item->getId().']['.$item->getQuoteItemId().'][address]')
            ->setName('ship['.$index.']['.$item->getQuoteItemId().'][address]')
            ->setValue($item->getCustomerAddressId())
            ->setOptions($this->getAddressOptions());
            
        return $select->getHtml();
    }
    
    /**
     * Retrieve options for addresses dropdown
     * 
     * @return array
     */
    public function getAddressOptions()
    {
        $options = $this->getData('address_options');
        if (is_null($options)) {
            $options = array();
            foreach ($this->getCustomer()->getLoadedAddressCollection() as $address) {
                $options[] = array(
                    'value'=>$address->getId(), 
                    'label'=>$address->getFirstname().' '.$address->getLastname().', '.
                        $address->getStreet(-1).', '.
                        $address->getCity().', '.
                        $address->getRegion().' '.
                        $address->getPostcode(),
                );
            }
            $this->setData('address_options', $options);
        }
        return $options;
    }
    
    public function getCustomer()
    {
        return $this->getCheckout()->getCustomerSession()->getCustomer();
    }
    
    public function getItemUrl($item)
    {
        return $this->getUrl('catalog/product/view/id/'.$item->getProductId());
    }
    
    public function getItemDeleteUrl($item)
    {
        return $this->getUrl('*/*/removeItem', array('address'=>$item->getParentId(), 'id'=>$item->getId()));
    }
    
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/addressesPost');
    }
    
    public function getNewAddressUrl()
    {
        return Mage::getUrl('*/multishipping_address/newShipping');
    }
    
    public function getBackUrl()
    {
        return Mage::getUrl('*/cart/');
    }
}
