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
namespace Magento\Checkout\Controller\Multishipping;

class Address extends \Magento\App\Action\Action
{
    /**
     * Retrieve multishipping checkout model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping
     */
    protected function _getCheckout()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Type\Multishipping');
    }

    /**
     * Retrieve checkout state model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping\State
     */
    protected function _getState()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Type\Multishipping\State');
    }


    /**
     * Create New Shipping address Form
     */
    public function newShippingAction()
    {
        $this->_getState()->setActiveStep(\Magento\Checkout\Model\Type\Multishipping\State::STEP_SELECT_ADDRESSES);
        $this->_layoutServices->loadLayout();
        $this->_layoutServices->getLayout()->initMessages('Magento\Customer\Model\Session');
        if ($addressForm = $this->_layoutServices->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Create Shipping Address'))
                ->setSuccessUrl($this->_url->getUrl('*/*/shippingSaved'))
                ->setErrorUrl($this->_url->getUrl('*/*/*'));

            if ($headBlock = $this->_layoutServices->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }

            if ($this->_getCheckout()->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl($this->_url->getUrl('*/multishipping/addresses'));
            } else {
                $addressForm->setBackUrl($this->_url->getUrl('*/cart/'));
            }
        }
        $this->_layoutServices->renderLayout();
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
        $this->_getState()->setActiveStep(\Magento\Checkout\Model\Type\Multishipping\State::STEP_SHIPPING);
        $this->_layoutServices->loadLayout();
        $this->_layoutServices->getLayout()->initMessages('Magento\Customer\Model\Session');
        if ($addressForm = $this->_layoutServices->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Edit Shipping Address'))
                ->setSuccessUrl($this->_url->getUrl('*/*/editShippingPost', array('id'=>$this->getRequest()->getParam('id'))))
                ->setErrorUrl($this->_url->getUrl('*/*/*'));

            if ($headBlock = $this->_layoutServices->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }

            if ($this->_getCheckout()->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl($this->_url->getUrl('*/multishipping/shipping'));
            }
        }
        $this->_layoutServices->renderLayout();
    }

    public function editShippingPostAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            $this->_objectManager->create('Magento\Checkout\Model\Type\Multishipping')
                ->updateQuoteCustomerShippingAddress($addressId);
        }
        $this->_redirect('*/multishipping/shipping');
    }

    public function selectBillingAction()
    {
        $this->_getState()->setActiveStep(\Magento\Checkout\Model\Type\Multishipping\State::STEP_BILLING);
        $this->_layoutServices->loadLayout();
        $messageStores = array('Magento\Customer\Model\Session', 'Magento\Catalog\Model\Session');
        $this->_layoutServices->getLayout()->initMessages($messageStores);
        $this->_layoutServices->renderLayout();
    }

    public function newBillingAction()
    {
        $this->_layoutServices->loadLayout();
        $this->_layoutServices->getLayout()->initMessages('Magento\Customer\Model\Session');
        if ($addressForm = $this->_layoutServices->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Create Billing Address'))
                ->setSuccessUrl($this->_url->getUrl('*/*/selectBilling'))
                ->setErrorUrl($this->_url->getUrl('*/*/*'))
                ->setBackUrl($this->_url->getUrl('*/*/selectBilling'));

            if ($headBlock = $this->_layoutServices->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->_layoutServices->renderLayout();
    }

    public function editAddressAction()
    {
        $this->_layoutServices->loadLayout();
        $this->_layoutServices->getLayout()->initMessages('Magento\Customer\Model\Session');
        if ($addressForm = $this->_layoutServices->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Edit Address'))
                ->setSuccessUrl($this->_url->getUrl('*/*/selectBilling'))
                ->setErrorUrl($this->_url->getUrl('*/*/*', array('id'=>$this->getRequest()->getParam('id'))))
                ->setBackUrl($this->_url->getUrl('*/*/selectBilling'));

            if ($headBlock = $this->_layoutServices->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->_layoutServices->renderLayout();
    }

    public function editBillingAction()
    {
        $this->_getState()->setActiveStep(
            \Magento\Checkout\Model\Type\Multishipping\State::STEP_BILLING
        );
        $this->_layoutServices->loadLayout();
        $this->_layoutServices->getLayout()->initMessages('Magento\Customer\Model\Session');
        if ($addressForm = $this->_layoutServices->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(__('Edit Billing Address'))
                ->setSuccessUrl($this->_url->getUrl('*/*/saveBilling', array('id'=>$this->getRequest()->getParam('id'))))
                ->setErrorUrl($this->_url->getUrl('*/*/*', array('id'=>$this->getRequest()->getParam('id'))))
                ->setBackUrl($this->_url->getUrl('*/multishipping/overview'));
            if ($headBlock = $this->_layoutServices->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->_layoutServices->renderLayout();
    }

    public function setBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            $this->_objectManager->create('Magento\Checkout\Model\Type\Multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/multishipping/billing');
    }

    public function saveBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            $this->_objectManager->create('Magento\Checkout\Model\Type\Multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/multishipping/overview');
    }
}
