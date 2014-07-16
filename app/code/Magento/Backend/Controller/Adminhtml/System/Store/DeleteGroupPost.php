<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Store;

class DeleteGroupPost extends \Magento\Backend\Controller\Adminhtml\System\Store
{
    /**
     * @return void
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam('item_id');

        if (!($model = $this->_objectManager->create('Magento\Store\Model\Group')->load($itemId))) {
            $this->messageManager->addError(__('Unable to proceed. Please, try again.'));
            $this->_redirect('adminhtml/*/');
            return;
        }
        if (!$model->isCanDelete()) {
            $this->messageManager->addError(__('This store cannot be deleted.'));
            $this->_redirect('adminhtml/*/editGroup', array('group_id' => $model->getId()));
            return;
        }

        $this->_backupDatabase('*/*/editGroup', array('group_id' => $itemId));

        try {
            $model->delete();
            $this->messageManager->addSuccess(__('The store has been deleted.'));
            $this->_redirect('adminhtml/*/');
            return;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to delete store. Please, try again later.'));
        }
        $this->_redirect('adminhtml/*/editGroup', array('group_id' => $itemId));
    }
}
