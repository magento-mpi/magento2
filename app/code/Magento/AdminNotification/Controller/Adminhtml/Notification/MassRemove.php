<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdminNotification\Controller\Adminhtml\Notification;

class MassRemove extends \Magento\AdminNotification\Controller\Adminhtml\Notification
{
    /**
     * @return void
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select messages.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = $this->_objectManager->create('Magento\AdminNotification\Model\Inbox')->load($id);
                    if ($model->getId()) {
                        $model->setIsRemove(1)->save();
                    }
                }
                $this->messageManager->addSuccess(__('Total of %1 record(s) have been removed.', count($ids)));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __("We couldn't remove the messages because of an error."));
            }
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }
}
