<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping checkout address matipulation controller
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Controller_Multishipping_Address extends Magento_Core_Controller_Front_Action
{
    /**
     * Retrieve multishipping checkout model
     *
     * @return Magento_Checkout_Model_Type_Multishipping
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Type_Multishipping');
    }

    /**
     * Retrieve checkout state model
     *
     * @return Magento_Checkot_Model_Type_Multishipping_State
     */
    protected function _getState()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Type_Multishipping_State');
    }


    /**
     * Create New Shipping address Form
     */
    public function newShippingAction()
    {
        $this->_getState()->setActiveStep(Magento_Checkout_Model_Type_Multishipping_State::STEP_SELECT_ADDRESSES);
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
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
        if (count($this->_getCheckout()->getCustomer()->getAddresses()) == 1) {
            $this->_getCheckout()->reset();
        }
        $this->_redirect('*/multishipping/addresses');
    }

    public function editShippingAction()
    {
        $this->_getState()->setActiveStep(Magento_Checkout_Model_Type_Multishipping_State::STEP_SHIPPING);
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
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
            Mage::getModel('Magento_Checkout_Model_Type_Multishipping')
                ->updateQuoteCustomerShippingAddress($addressId);
        }
        $this->_redirect('*/multishipping/shipping');
    }

    public function selectBillingAction()
    {
        $this->_getState()->setActiveStep(Magento_Checkout_Model_Type_Multishipping_State::STEP_BILLING);
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $this->_initLayoutMessages('Magento_Checkout_Model_Session');
        $this->renderLayout();
    }

    public function newBillingAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
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
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
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
        $this->_getState()->setActiveStep(
            Magento_Checkout_Model_Type_Multishipping_State::STEP_BILLING
        );
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
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
            Mage::getModel('Magento_Checkout_Model_Type_Multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/multishipping/billing');
    }

    public function saveBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            Mage::getModel('Magento_Checkout_Model_Type_Multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/multishipping/overview');
    }
}
