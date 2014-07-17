<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Store;

class DeleteWebsitePost extends \Magento\Backend\Controller\Adminhtml\System\Store
{
    /**
     * @return void
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam('item_id');
        $model = $this->_objectManager->create('Magento\Store\Model\Website');
        $model->load($itemId);

        if (!$model) {
            $this->messageManager->addError(__('Unable to proceed. Please, try again'));
            $this->_redirect('adminhtml/*/');
            return;
        }
        if (!$model->isCanDelete()) {
            $this->messageManager->addError(__('This website cannot be deleted.'));
            $this->_redirect('adminhtml/*/editWebsite', array('website_id' => $model->getId()));
            return;
        }

        $this->_backupDatabase('*/*/editWebsite', array('website_id' => $itemId));

        try {
            $model->delete();
            $this->messageManager->addSuccess(__('The website has been deleted.'));
            $this->_redirect('adminhtml/*/');
            return;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to delete website. Please, try again later.'));
        }
        $this->_redirect('adminhtml/*/editWebsite', array('website_id' => $itemId));
    }
}
