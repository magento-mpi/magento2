<?php
/**
 * Adminhtml AdminNotification controller
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Adminhtml_NotificationController extends Mage_Backend_Controller_ActionAbstract
{
    public function indexAction()
    {
        $this->_title(__('Notifications'));

        $this->loadLayout()
            ->_setActiveMenu('Mage_AdminNotification::system_adminnotification')
            ->_addBreadcrumb(
                __('Messages Inbox'),
                __('Messages Inbox')
            )->renderLayout();
    }

    public function markAsReadAction()
    {
        $notificationId = (int)$this->getRequest()->getParam('id');
        if ($notificationId) {
            try {
                $this->_objectManager->create('Mage_AdminNotification_Model_NotificationService')
                    ->markAsRead($notificationId);
                $this->_session->addSuccess(
                    __('The message has been marked as Read.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_session->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_session->addException($e,
                    __("We couldn't mark the notification as Read because of an error.")
                );
            }

            $this->_redirectReferer();
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Mark notification as read (AJAX action)
     */
    public function ajaxMarkAsReadAction()
    {
        if (!$this->getRequest()->getPost()) {
            return;
        }
        $notificationId = (int)$this->getRequest()->getPost('id');
        $responseData = array();
        try {
            $this->_objectManager->create('Mage_AdminNotification_Model_NotificationService')
                ->markAsRead($notificationId);
            $responseData['success'] = true;
        } catch (Exception $e) {
            $responseData['success'] = false;
        }
        $this->getResponse()->setBody(
            $this->_objectManager->create('Mage_Core_Helper_Data')->jsonEncode($responseData)
        );
    }

    public function massMarkAsReadAction()
    {
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $this->_session->addError(__('Please select messages.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('Mage_AdminNotification_Model_Inbox')
                        ->load($id);
                    if ($model->getId()) {
                        $model->setIsRead(1)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    __('A total of %1 record(s) have been marked as Read.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_session->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_session->addException($e,
                    __("We couldn't mark the notification as Read because of an error.")
                );
            }
        }
        $this->_redirect('*/*/');
    }

    public function removeAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $model = Mage::getModel('Mage_AdminNotification_Model_Inbox')
                ->load($id);

            if (!$model->getId()) {
                $this->_redirect('*/*/');
                return ;
            }

            try {
                $model->setIsRemove(1)
                    ->save();
                $this->_session->addSuccess(
                    __('The message has been removed.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_session->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_session->addException($e,
                    __("We couldn't remove the messages because of an error.")
                );
            }

            $this->_redirect('*/*/');
            return;
        }
        $this->_redirect('*/*/');
    }

    public function massRemoveAction()
    {
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $this->_session->addError(
                __('Please select messages.')
            );
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('Mage_AdminNotification_Model_Inbox')
                        ->load($id);
                    if ($model->getId()) {
                        $model->setIsRemove(1)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    __('Total of %1 record(s) have been removed.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e,
                    __("We couldn't remove the messages because of an error."));
            }
        }
        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'markAsRead':
                $acl = 'Mage_AdminNotification::mark_as_read';
                break;

            case 'massMarkAsRead':
                $acl = 'Mage_AdminNotification::mark_as_read';
                break;

            case 'remove':
                $acl = 'Mage_AdminNotification::adminnotification_remove';
                break;

            case 'massRemove':
                $acl = 'Mage_AdminNotification::adminnotification_remove';
                break;

            default:
                $acl = 'Mage_AdminNotification::show_list';
        }
        return $this->_authorization->isAllowed($acl);
    }
}
