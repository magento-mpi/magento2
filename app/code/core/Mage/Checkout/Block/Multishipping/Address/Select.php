<?php
/**
 * Multishipping checkout select billing address
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Block_Multishipping_Address_Select extends Mage_Checkout_Block_Multishipping_Abstract
{
    protected function _initChildren()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(__('Change Billing Address') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_initChildren();
    }
    
    public function getAddressCollection()
    {
        $collection = $this->getData('address_collection');
        if (is_null($collection)) {
            $collection = Mage::getSingleton('checkout/type_multishipping')->getCustomer()->getLoadedAddressCollection();
            $this->setData('address_collection', $collection);
        }
        return $collection;
    }
    
    public function getEditAddressUrl($address)
    {
        return $this->getUrl('*/*/editBilling', array('id'=>$address->getId()));
    }
    
    public function getSetAddressUrl($address)
    {
        return $this->getUrl('*/*/setBilling', array('id'=>$address->getId()));
    }
    
    public function getAddNewUrl()
    {
        return $this->getUrl('*/*/newBilling');
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/multishipping/billing');
    }
}
