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
    /**
     * Retrieve multishipping checkout model
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/type_multishipping');
    }
    
    /**
     * Create New Shipping address Form
     */
    public function newShippingAction()
    {
        $this->loadLayout(array('default', 'multishipping', 'customer_address'), 'multishipping_addresses');
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Create Shipping Address'))
                ->setSuccessUrl(Mage::getUrl('*/*/shippingSaved'))
                ->setErrorUrl(Mage::getUrl('*/*/*'));
                
            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
            
            if ($this->_getCheckout()->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl(Mage::getUrl('*/multishipping/addresses'));
            }
            else {
                $addressForm->setBackUrl(Mage::getUrl('*/cart/'));
            }
        }
        $this->renderLayout();
    }
    
    public function shippingSavedAction()
    {
        /**
         * if we create first address we need reset emd init checkout
         */
        if ($this->_getCheckout()->getCustomer()->getLoadedAddressCollection()->getSize() == 1) {
            $this->_getCheckout()->reset();
        }
        $this->_redirect('*/multishipping/addresses');
    }
    
    public function editShippingAction()
    {
        $this->loadLayout(array('default', 'multishipping', 'customer_address'), 'multishipping_addresses');
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Edit Shipping Address'))
                ->setSuccessUrl(Mage::getUrl('*/*/editShippingPost', array('id'=>$this->getRequest()->getParam('id'))))
                ->setErrorUrl(Mage::getUrl('*/*/*'));
                
            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
            
            if ($this->_getCheckout()->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl(Mage::getUrl('*/multishipping/shipping'));
            }
        }
        $this->renderLayout();
    }
    
    public function editShippingPostAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            Mage::getModel('checkout/type_multishipping')
                ->updateQuoteCustomerShippingAddress($addressId);
        }
        $this->_redirect('*/multishipping/shipping');
    }
    
    public function selectBillingAction()
    {
        $this->loadLayout(array('default', 'multishipping', 'multishipping_address_select'), 'multishipping_address_select');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }
    
    public function newBillingAction()
    {
        $this->loadLayout(array('default', 'multishipping', 'customer_address'), 'multishipping_addresses');
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Create Billing Address'))
                ->setSuccessUrl(Mage::getUrl('*/*/selectBilling'))
                ->setErrorUrl(Mage::getUrl('*/*/*'))
                ->setBackUrl(Mage::getUrl('*/*/selectBilling'));
                
            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->renderLayout();
    }
    
    public function editAddressAction()
    {
        $this->loadLayout(array('default', 'multishipping', 'customer_address'), 'multishipping_addresses');
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Edit Address'))
                ->setSuccessUrl(Mage::getUrl('*/*/selectBilling'))
                ->setErrorUrl(Mage::getUrl('*/*/*', array('id'=>$this->getRequest()->getParam('id'))))
                ->setBackUrl(Mage::getUrl('*/*/selectBilling'));
                
            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->renderLayout();
    }
    
    public function editBillingAction()
    {
        $this->loadLayout(array('default', 'multishipping', 'customer_address'), 'multishipping_addresses');
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Edit Billing Address'))
                ->setSuccessUrl(Mage::getUrl('*/*/saveBilling', array('id'=>$this->getRequest()->getParam('id'))))
                ->setErrorUrl(Mage::getUrl('*/*/*', array('id'=>$this->getRequest()->getParam('id'))))
                ->setBackUrl(Mage::getUrl('*/multishipping/overview'));
            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->renderLayout();
    }
    
    public function setBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            Mage::getModel('checkout/type_multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/multishipping/billing');
    }
    
    public function saveBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            Mage::getModel('checkout/type_multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/multishipping/overview');
    }
}
