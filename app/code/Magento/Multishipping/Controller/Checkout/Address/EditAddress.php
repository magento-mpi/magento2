<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Controller\Checkout\Address;

class EditAddress extends \Magento\Multishipping\Controller\Checkout\Address
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($addressForm = $this->_view->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(
                __('Edit Address')
            )->setSuccessUrl(
                $this->_url->getUrl('*/*/selectBilling')
            )->setErrorUrl(
                $this->_url->getUrl('*/*/*', array('id' => $this->getRequest()->getParam('id')))
            )->setBackUrl(
                $this->_url->getUrl('*/*/selectBilling')
            );
            $this->pageConfig->setTitle($addressForm->getTitle() . ' - ' . $this->pageConfig->getDefaultTitle());
        }
        $this->_view->renderLayout();
    }
}
