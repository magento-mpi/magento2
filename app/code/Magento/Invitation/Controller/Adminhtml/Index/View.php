<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Adminhtml\Index;

class View extends \Magento\Invitation\Controller\Adminhtml\Index
{
    /**
     * Invitation view action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_initInvitation();
            $this->_view->loadLayout();
            $this->_setActiveMenu('Magento_Invitation::customer_magento_invitation');
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Invitations'));
            $this->_view->renderLayout();
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('invitations/*/');
        }
    }
}
