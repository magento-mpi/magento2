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
namespace Magento\Multishipping\Controller\Checkout;

class Address extends \Magento\App\Action\Action
{
    /**
     * Retrieve multishipping checkout model
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    protected function _getCheckout()
    {
        return $this->_objectManager->get('Magento\Multishipping\Model\Checkout\Type\Multishipping');
    }

    /**
     * Retrieve checkout state model
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping\State
     */
    protected function _getState()
    {
        return $this->_objectManager->get('Magento\Multishipping\Model\Checkout\Type\Multishipping\State');
    }


    /**
     * Create New Shipping address Form
     */
    public function newShippingAction()
    {
        $this->_getState()->setActiveStep(
            \Magento\Multishipping\Model\Checkout\Type\Multishipping\State::STEP_SELECT_ADDRESSES
        );
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($addressForm = $this->_view->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Create Shipping Address'))
                ->setSuccessUrl($this->_url->getUrl('*/*/shippingSaved'))
                ->setErrorUrl($this->_url->getUrl('*/*/*'));

            if ($headBlock = $this->_view->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }

            if ($this->_getCheckout()->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl($this->_url->getUrl('*/checkout/addresses'));
            } else {
                $addressForm->setBackUrl($this->_url->getUrl('*/cart/'));
            }
        }
        $this->_view->renderLayout();
    }

    public function shippingSavedAction()
    {
        /**
         * if we create first address we need reset emd init checkout
         */
        if (count($this->_getCheckout()->getCustomer()->getAddresses()) == 1) {
            $this->_getCheckout()->reset();
        }
        $this->_redirect('*/checkout/addresses');
    }

    public function editShippingAction()
    {
        $this->_getState()->setActiveStep(
            \Magento\Multishipping\Model\Checkout\Type\Multishipping\State::STEP_SHIPPING
        );
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($addressForm = $this->_view->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Edit Shipping Address'))
                ->setSuccessUrl($this->_url->getUrl('*/*/editShippingPost', array('id'=>$this->getRequest()->getParam('id'))))
                ->setErrorUrl($this->_url->getUrl('*/*/*'));

            if ($headBlock = $this->_view->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }

            if ($this->_getCheckout()->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl($this->_url->getUrl('*/multishipping/shipping'));
            }
        }
        $this->_view->renderLayout();
    }

    public function editShippingPostAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            $this->_objectManager->create('Magento\Multishipping\Model\Checkout\Type\Multishipping')
                ->updateQuoteCustomerShippingAddress($addressId);
        }
        $this->_redirect('*/multishipping/shipping');
    }

    public function selectBillingAction()
    {
        $this->_getState()->setActiveStep(\Magento\Multishipping\Model\Checkout\Type\Multishipping\State::STEP_BILLING);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    public function newBillingAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($addressForm = $this->_view->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Create Billing Address'))
                ->setSuccessUrl($this->_url->getUrl('*/*/selectBilling'))
                ->setErrorUrl($this->_url->getUrl('*/*/*'))
                ->setBackUrl($this->_url->getUrl('*/*/selectBilling'));

            if ($headBlock = $this->_view->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->_view->renderLayout();
    }

    public function editAddressAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($addressForm = $this->_view->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Edit Address'))
                ->setSuccessUrl($this->_url->getUrl('*/*/selectBilling'))
                ->setErrorUrl($this->_url->getUrl('*/*/*', array('id'=>$this->getRequest()->getParam('id'))))
                ->setBackUrl($this->_url->getUrl('*/*/selectBilling'));

            if ($headBlock = $this->_view->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->_view->renderLayout();
    }

    public function editBillingAction()
    {
        $this->_getState()->setActiveStep(
            \Magento\Multishipping\Model\Checkout\Type\Multishipping\State::STEP_BILLING
        );
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($addressForm = $this->_view->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Edit Billing Address'))
                ->setSuccessUrl($this->_url->getUrl('*/*/saveBilling', array('id'=>$this->getRequest()->getParam('id'))))
                ->setErrorUrl($this->_url->getUrl('*/*/*', array('id'=>$this->getRequest()->getParam('id'))))
                ->setBackUrl($this->_url->getUrl('*/multishipping/overview'));
            if ($headBlock = $this->_view->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->_view->renderLayout();
    }

    public function setBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            $this->_objectManager->create('Magento\Multishipping\Model\Checkout\Type\Multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/multishipping/billing');
    }

    public function saveBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            $this->_objectManager->create('Magento\Multishipping\Model\Checkout\Type\Multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/multishipping/overview');
    }
}
