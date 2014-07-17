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
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Delete Store View'));

        $itemId = $this->getRequest()->getParam('item_id', null);
        if (!($model = $this->_objectManager->create('Magento\Store\Model\Store')->load($itemId))) {
            $this->messageManager->addError(__('Unable to proceed. Please, try again.'));
            $this->_redirect('adminhtml/*/');
            return;
        }
        if (!$model->isCanDelete()) {
            $this->messageManager->addError(__('This store view cannot be deleted.'));
            $this->_redirect('adminhtml/*/editStore', array('store_id' => $itemId));
            return;
        }

        $this->_addDeletionNotice('store view');

        $this->_initAction()->_addBreadcrumb(
            __('Delete Store View'),
            __('Delete Store View')
        )->_addContent(
            $this->_view->getLayout()->createBlock(
                'Magento\Backend\Block\System\Store\Delete'
            )->setFormActionUrl(
                $this->getUrl('adminhtml/*/deleteStorePost')
            )->setBackUrl(
                $this->getUrl('adminhtml/*/editStore', array('store_id' => $itemId))
            )->setStoreTypeTitle(
                __('Store View')
            )->setDataObject(
                $model
            )
        );
        $this->_view->renderLayout();
    }
}
