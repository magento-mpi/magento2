<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Controller\Adminhtml\User;

class InvalidateToken extends \Magento\User\Controller\Adminhtml\User
{
    /**
     * @return void
     */
    public function execute()
    {
        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
        $userSession = $this->_objectManager->get('Magento\Backend\Model\Auth\Session');

        if ($userId = $this->getRequest()->getParam('user_id')) {
            try {
                /** @var \Magento\User\Model\User $model */
                /*
                 * TODO: insert code here to revoke all tokens
                 */
                $this->messageManager->addSuccess(__('You have invalidated the user.'));
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('user_id' => $this->getRequest()->getParam('user_id')));
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a user to invalidate.'));
        $this->_redirect('adminhtml/*/');
    }
}
