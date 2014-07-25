<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Store;

class DeleteWebsite extends \Magento\Backend\Controller\Adminhtml\System\Store
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Delete Web Site'));

        $itemId = $this->getRequest()->getParam('item_id', null);
        if (!($model = $this->_objectManager->create('Magento\Store\Model\Website')->load($itemId))) {
            $this->messageManager->addError(__('Unable to proceed. Please, try again.'));
            $this->_redirect('adminhtml/*/');
            return;
        }
        if (!$model->isCanDelete()) {
            $this->messageManager->addError(__('This website cannot be deleted.'));
            $this->_redirect('adminhtml/*/editWebsite', array('website_id' => $itemId));
            return;
        }

        $this->_addDeletionNotice('website');

        $this->_initAction()->_addBreadcrumb(
            __('Delete Web Site'),
            __('Delete Web Site')
        )->_addContent(
            $this->_view->getLayout()->createBlock(
                'Magento\Backend\Block\System\Store\Delete'
            )->setFormActionUrl(
                $this->getUrl('adminhtml/*/deleteWebsitePost')
            )->setBackUrl(
                $this->getUrl('adminhtml/*/editWebsite', array('website_id' => $itemId))
            )->setStoreTypeTitle(
                __('Web Site')
            )->setDataObject(
                $model
            )
        );
        $this->_view->renderLayout();
    }
}
