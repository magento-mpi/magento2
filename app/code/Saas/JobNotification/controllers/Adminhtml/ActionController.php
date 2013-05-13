<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_JobNotification_Adminhtml_ActionController extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * @var Saas_JobNotification_Service_Notification
     */
    protected $_service;

    /**
     * @param Mage_Backend_Controller_Context $context
     * @param Saas_JobNotification_Service_Notification $service
     * @param Saas_JobNotification_Helper_Data $helper
     * @param null $areaCode
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Saas_JobNotification_Service_Notification $service,
        Saas_JobNotification_Helper_Data $helper,
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);
        $this->_helper = $helper;
        $this->_service = $service;
    }

    /**
     * Check whether controller actions is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Saas_JobNotification::notification_grid');
    }

    /**
     * Mark item as read action
     */
    public function markAsReadAction()
    {
        if ($this->_authorization->isAllowed('Saas_JobNotification::notification_action_markread')) {
            $notificationId = $this->getRequest()->getParam('id');
            try {
                $this->_service->update($notificationId, array('is_read' => 1));
                $this->_session->addSuccess($this->_helper->__('The notification has been marked as read'));
            } catch (InvalidArgumentException $exception) {
                $this->_session->addError($this->_helper->__('Unable to proceed. Please, try again'));
            } catch (Saas_JobNotification_Service_Exception $exception) {
                $this->_session->addError($exception->getMessage());
            } catch (Exception $exception) {
                $this->_session->addException(
                    $exception,
                    $this->_helper->__('An error occurred while marking notification as read')
                );
            }
        }
        $this->_redirect('*/view/index');
    }

    /**
     * Mass mark as read action
     */
    public function massMarkAsReadAction()
    {
        if ($this->_authorization->isAllowed('Saas_JobNotification::notification_action_markread')) {
            $notificationIds = $this->getRequest()->getParam('notification_ids');
            try {
                $this->_service->massUpdate($notificationIds, array('is_read' => 1));
                $this->_session->addSuccess(
                    $this->_helper->__('Total of %d record(s) have been marked as read', count($notificationIds))
                );
            } catch (InvalidArgumentException $exception) {
                $this->_session->addError($this->_helper->__('Please select notifications'));
            } catch (Saas_JobNotification_Service_Exception $exception) {
                $this->_session->addError($exception->getMessage());
            } catch (Exception $exception) {
                $this->_session->addException(
                    $exception,
                    $this->_helper->__('An error occurred while marking the notifications as read')
                );
            }
        }
        $this->_redirect('*/view/index');
    }

    /**
     * Remove item action
     */
    public function removeAction()
    {
        if ($this->_authorization->isAllowed('Saas_JobNotification::notification_action_remove')) {
            $notificationId = $this->getRequest()->getParam('id');
            try {
                $this->_service->update($notificationId, array('is_remove' => 1));
                $this->_session->addSuccess($this->_helper->__('The notification has been removed'));
            } catch (InvalidArgumentException $exception) {
                $this->_session->addError($this->_helper->__('Unable to proceed. Please, try again'));
            } catch (Saas_JobNotification_Service_Exception $exception) {
                $this->_session->addError($exception->getMessage());
            } catch (Exception $exception) {
                $this->_session->addException(
                    $exception,
                    $this->_helper->__('An error occurred while removing the notification')
                );
            }
        }
        $this->_redirect('*/view/index');
    }

    /**
     * Mass remove notifications action
     */
    public function massRemoveAction()
    {
        if ($this->_authorization->isAllowed('Saas_JobNotification::notification_action_remove')) {
            $notificationIds = $this->getRequest()->getParam('notification_ids');
            try {
                $this->_service->massUpdate($notificationIds, array('is_remove' => 1));
                $this->_session->addSuccess(
                    $this->_helper->__('Total of %d record(s) have been removed', count($notificationIds))
                );
            } catch (InvalidArgumentException $exception) {
                $this->_session->addError($this->_helper->__('Please select notifications'));
            } catch (Saas_JobNotification_Service_Exception $exception) {
                $this->_session->addError($exception->getMessage());
            } catch (Exception $exception) {
                $this->_session->addException(
                    $exception,
                    $this->_helper->__('An error occurred while removing notifications')
                );
            }
        }
        $this->_redirect('*/view/index');
    }
}
