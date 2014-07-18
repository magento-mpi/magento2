<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Controller\Adminhtml\User;

class Save extends \Magento\User\Controller\Adminhtml\User
{
    /**
     * @return void
     */
    public function execute()
    {
        $userId = (int)$this->getRequest()->getParam('user_id');
        $data = $this->getRequest()->getPost();
        if (!$data) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        /** @var $model \Magento\User\Model\User */
        $model = $this->_userFactory->create()->load($userId);
        if ($userId && $model->isObjectNew()) {
            $this->messageManager->addError(__('This user no longer exists.'));
            $this->_redirect('adminhtml/*/');
            return;
        }
        $model->setData($this->_getAdminUserData($data));
        $uRoles = $this->getRequest()->getParam('roles', array());
        if (count($uRoles)) {
            $model->setRoleId($uRoles[0]);
        }

        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
        if ($userId == $currentUser->getId() && $this->_objectManager->get(
            'Magento\Framework\Locale\Validator'
        )->isValid(
            $data['interface_locale']
        )
        ) {
            $this->_objectManager->get(
                'Magento\Backend\Model\Locale\Manager'
            )->switchBackendInterfaceLocale(
                $data['interface_locale']
            );
        }

        try {
            $model->save();
            $this->messageManager->addSuccess(__('You saved the user.'));
            $this->_getSession()->setUserData(false);
            $this->_redirect('adminhtml/*/');
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addMessages($e->getMessages());
            $this->_getSession()->setUserData($data);
            $this->_redirect('adminhtml/*/edit', array('_current' => true));
        }
    }
}
