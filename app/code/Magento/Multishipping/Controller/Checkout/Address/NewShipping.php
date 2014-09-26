<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Controller\Checkout\Address;

class NewShipping extends \Magento\Multishipping\Controller\Checkout\Address
{
    /**
     * Create New Shipping address Form
     *
     * @return void
     */
    public function execute()
    {
        $this->_getState()->setActiveStep(
            \Magento\Multishipping\Model\Checkout\Type\Multishipping\State::STEP_SELECT_ADDRESSES
        );
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($addressForm = $this->_view->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(
                __('Create Shipping Address')
            )->setSuccessUrl(
                $this->_url->getUrl('*/*/shippingSaved')
            )->setErrorUrl(
                $this->_url->getUrl('*/*/*')
            );

            $this->_view->getPage()->getConfig()->setTitle(
                $addressForm->getTitle() . ' - ' . $this->_view->getPage()->getConfig()->getDefaultTitle()
            );

            if ($this->_getCheckout()->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl($this->_url->getUrl('*/checkout/addresses'));
            } else {
                $addressForm->setBackUrl($this->_url->getUrl('*/cart/'));
            }
        }
        $this->_view->renderLayout();
    }
}
