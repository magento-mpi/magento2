<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CheckoutAgreements\Controller\Adminhtml\Agreement;

class Index extends \Magento\CheckoutAgreements\Controller\Adminhtml\Agreement
{
    /**
     * @return void
     */
    public function execute()
    {
//        $this->_title->add(__('Terms and Conditions'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Terms and Conditions'));

        $this->_initAction()->_addContent(
            $this->_view->getLayout()->createBlock('Magento\CheckoutAgreements\Block\Adminhtml\Agreement')
        );
        $this->_view->renderLayout();
    }
}
