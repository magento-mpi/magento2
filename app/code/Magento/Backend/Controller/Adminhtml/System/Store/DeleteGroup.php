<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Store;

class DeleteGroup extends \Magento\Backend\Controller\Adminhtml\System\Store
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Delete Store'));

        $itemId = $this->getRequest()->getParam('item_id', null);
        if (!($model = $this->_objectManager->create('Magento\Store\Model\Group')->load($itemId))) {
            $this->messageManager->addError(__('Unable to proceed. Please, try again.'));
            $this->_redirect('adminhtml/*/');
            return;
        }
        if (!$model->isCanDelete()) {
            $this->messageManager->addError(__('This store cannot be deleted.'));
            $this->_redirect('adminhtml/*/editGroup', array('group_id' => $itemId));
            return;
        }

        $this->_addDeletionNotice('store');

        $this->_initAction()->_addBreadcrumb(
            __('Delete Store'),
            __('Delete Store')
        )->_addContent(
            $this->_view->getLayout()->createBlock(
                'Magento\Backend\Block\System\Store\Delete'
            )->setFormActionUrl(
                $this->getUrl('adminhtml/*/deleteGroupPost')
            )->setBackUrl(
                $this->getUrl('adminhtml/*/editGroup', array('group_id' => $itemId))
            )->setStoreTypeTitle(
                __('Store')
            )->setDataObject(
                $model
            )
        );
        $this->_view->renderLayout();
    }
}
