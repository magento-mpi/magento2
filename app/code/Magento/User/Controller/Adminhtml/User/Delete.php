<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Controller\Adminhtml\User;

class Delete extends \Magento\User\Controller\Adminhtml\User
{
    /**
     * @return void
     */
    public function execute()
    {
        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();

        if ($userId = $this->getRequest()->getParam('user_id')) {
            if ($currentUser->getId() == $userId) {
                $this->messageManager->addError(__('You cannot delete your own account.'));
                $this->_redirect('adminhtml/*/edit', array('user_id' => $userId));
                return;
            }
            try {
                /** @var \Magento\User\Model\User $model */
                $model = $this->_userFactory->create();
                $model->setId($userId);
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the user.'));
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('user_id' => $this->getRequest()->getParam('user_id')));
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a user to delete.'));
        $this->_redirect('adminhtml/*/');
    }
}
