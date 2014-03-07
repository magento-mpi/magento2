<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Controller\Checkout;

use Magento\App\Action\Context;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;

/**
 * Multishipping checkout address manipulation controller
 */
class Address extends \Magento\App\Action\Action
{
    /** @var CustomerAddressServiceInterface */
    protected $_customerAddressService;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param CustomerAddressServiceInterface $customerAddressService
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        CustomerAddressServiceInterface $customerAddressService
    ) {
        $this->_customerAddressService = $customerAddressService;
        parent::__construct($context);
    }

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
     *
     * @return void
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

    /**
     * @return void
     */
    public function shippingSavedAction()
    {
        /**
         * if we create first address we need reset emd init checkout
         */
        $customerId = $this->_getCheckout()->getCustomer()->getCustomerId();
        if (count($this->_customerAddressService->getAddresses($customerId)) == 1) {
            $this->_getCheckout()->reset();
        }
        $this->_redirect('*/checkout/addresses');
    }

    /**
     * @return void
     */
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
                $addressForm->setBackUrl($this->_url->getUrl('*/checkout/shipping'));
            }
        }
        $this->_view->renderLayout();
    }

    /**
     * @return void
     */
    public function editShippingPostAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            $this->_objectManager->create('Magento\Multishipping\Model\Checkout\Type\Multishipping')
                ->updateQuoteCustomerShippingAddress($addressId);
        }
        $this->_redirect('*/checkout/shipping');
    }

    /**
     * @return void
     */
    public function selectBillingAction()
    {
        $this->_getState()->setActiveStep(\Magento\Multishipping\Model\Checkout\Type\Multishipping\State::STEP_BILLING);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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
                ->setBackUrl($this->_url->getUrl('*/checkout/overview'));
            if ($headBlock = $this->_view->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->_view->renderLayout();
    }

    /**
     * @return void
     */
    public function setBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            $this->_objectManager->create('Magento\Multishipping\Model\Checkout\Type\Multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/checkout/billing');
    }

    /**
     * @return void
     */
    public function saveBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            $this->_objectManager->create('Magento\Multishipping\Model\Checkout\Type\Multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/checkout/overview');
    }
}
