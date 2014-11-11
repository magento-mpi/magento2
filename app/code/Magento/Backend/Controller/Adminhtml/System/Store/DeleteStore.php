<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Store;

class DeleteStore extends \Magento\Backend\Controller\Adminhtml\System\Store
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_title->add(__('Delete Store View'));

        $itemId = $this->getRequest()->getParam('item_id', null);
        if (!($model = $this->_objectManager->create('Magento\Store\Model\Store')->load($itemId))) {
            $this->messageManager->addError(__('Unable to proceed. Please, try again.'));
            $this->_redirect('adminhtml/*/');
            /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
            $redirectResult = $this->resultRedirectFactory->create();
            return $redirectResult->setPath('adminhtml/*/');
        }
        if (!$model->isCanDelete()) {
            $this->messageManager->addError(__('This store view cannot be deleted.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
            $redirectResult = $this->resultRedirectFactory->create();
            return $redirectResult->setPath('adminhtml/*/editStore', ['store_id' => $itemId]);
        }

        $this->_addDeletionNotice('store view');

        $resultPage = $this->createPage();
        $resultPage->addBreadcrumb(__('Delete Store View'), __('Delete Store View'))
            ->addContent(
                $resultPage->getLayout()->createBlock('Magento\Backend\Block\System\Store\Delete')
                    ->setFormActionUrl($this->getUrl('adminhtml/*/deleteStorePost'))
                    ->setBackUrl($this->getUrl('adminhtml/*/editStore', ['store_id' => $itemId]))
                    ->setStoreTypeTitle(__('Store View'))
                    ->setDataObject($model)
            );
        return $resultPage;
    }
}
