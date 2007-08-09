<?php
/**
 * Multishipping checkout address matipulation controller
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Multishipping_AddressController extends Mage_Core_Controller_Front_Action
{
    public function newShippingAction()
    {
        $this->loadLayout(array('default', 'multishipping', 'customer_address'), 'multishipping_addresses');
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Create Shipping Address'))
                ->setSuccessUrl(Mage::getUrl('*/multishipping/addresses'))
                ->setErrorUrl(Mage::getUrl('*/*/*'));
            
            if (Mage::getSingleton('checkout/type_multishipping')->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl(Mage::getUrl('*/multishipping/addresses'));
            }
            else {
                $addressForm->setBackUrl(Mage::getUrl('*/cart/'));
            }
        }
        $this->renderLayout();
    }
    
    public function editShippingAction()
    {
        $this->loadLayout(array('default', 'multishipping', 'customer_address'), 'multishipping_addresses');
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Create Shipping Address'))
                ->setSuccessUrl(Mage::getUrl('*/multishipping/shipping'))
                ->setErrorUrl(Mage::getUrl('*/*/*'));
            
            if (Mage::getSingleton('checkout/type_multishipping')->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl(Mage::getUrl('*/multishipping/shipping'));
            }
        }
        $this->renderLayout();
    }
    
    public function editBillingAction()
    {
        
    }
}
