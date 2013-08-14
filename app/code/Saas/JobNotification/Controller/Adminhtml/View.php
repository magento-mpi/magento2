<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_JobNotification_Controller_Adminhtml_View extends Mage_Backend_Controller_ActionAbstract
{
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
     * Index grid view action
     */
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('Saas_JobNotification::grid');
        $this->_title('Task Notifications');

        $this->renderLayout();
    }

    /**
     * Ajax grid view action
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
}
