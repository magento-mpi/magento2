<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CheckoutAgreements\Controller\Adminhtml\Agreement;

class Save extends \Magento\CheckoutAgreements\Controller\Adminhtml\Agreement
{
    /**
     * @return void
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPost();
        if ($postData) {
            $model = $this->_objectManager->get('Magento\CheckoutAgreements\Model\Agreement');
            $model->setData($postData);

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The condition has been saved.'));
                $this->_redirect('checkout/*/');

                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving this condition.'));
            }

            $this->_objectManager->get('Magento\Backend\Model\Session')->setAgreementData($postData);
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
        }
    }
}
