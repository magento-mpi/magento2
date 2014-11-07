<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CheckoutAgreements\Controller\Adminhtml\Agreement;

class Edit extends \Magento\CheckoutAgreements\Controller\Adminhtml\Agreement
{
    /**
     * @return void
     */
    public function execute()
    {
//        $this->_title->add(__('Terms and Conditions'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Terms and Conditions'));
        $id = $this->getRequest()->getParam('id');
        $agreementModel = $this->_objectManager->create('Magento\CheckoutAgreements\Model\Agreement');

        if ($id) {
            $agreementModel->load($id);
            if (!$agreementModel->getId()) {
                $this->messageManager->addError(__('This condition no longer exists.'));
                $this->_redirect('checkout/*/');
                return;
            }
        }

//        $this->_title->add($agreementModel->getId() ? $agreementModel->getName() : __('New Condition'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $agreementModel->getId() ? $agreementModel->getName() : __('New Condition')
        );

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getAgreementData(true);
        if (!empty($data)) {
            $agreementModel->setData($data);
        }

        $this->_coreRegistry->register('checkout_agreement', $agreementModel);

        $this->_initAction()->_addBreadcrumb(
            $id ? __('Edit Condition') : __('New Condition'),
            $id ? __('Edit Condition') : __('New Condition')
        )->_addContent(
            $this->_view->getLayout()->createBlock(
                'Magento\CheckoutAgreements\Block\Adminhtml\Agreement\Edit'
            )->setData(
                'action',
                $this->getUrl('checkout/*/save')
            )
        );
        $this->_view->renderLayout();
    }
}
