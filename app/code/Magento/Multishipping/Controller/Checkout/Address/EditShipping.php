<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Controller\Checkout\Address;

class EditShipping extends \Magento\Multishipping\Controller\Checkout\Address
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_getState()->setActiveStep(
            \Magento\Multishipping\Model\Checkout\Type\Multishipping\State::STEP_SHIPPING
        );
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($addressForm = $this->_view->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(
                __('Edit Shipping Address')
            )->setSuccessUrl(
                $this->_url->getUrl('*/*/editShippingPost', array('id' => $this->getRequest()->getParam('id')))
            )->setErrorUrl(
                $this->_url->getUrl('*/*/*')
            );

            $this->pageConfig->setTitle($addressForm->getTitle() . ' - ' . $this->pageConfig->getDefaultTitle());

            if ($this->_getCheckout()->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl($this->_url->getUrl('*/checkout/shipping'));
            }
        }
        $this->_view->renderLayout();
    }
}
