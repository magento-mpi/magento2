<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Controller\Adminhtml\Auth;

class ResetPasswordPost extends \Magento\User\Controller\Adminhtml\Auth
{
    /**
     * Reset forgotten password
     *
     * Used to handle data received from reset forgotten password form
     *
     * @return void
     */
    public function execute()
    {
        $passwordResetToken = (string)$this->getRequest()->getQuery('token');
        $userId = (int)$this->getRequest()->getQuery('id');
        $password = (string)$this->getRequest()->getPost('password');
        $passwordConfirmation = (string)$this->getRequest()->getPost('confirmation');

        try {
            $this->_validateResetPasswordLinkToken($userId, $passwordResetToken);
        } catch (\Exception $exception) {
            $this->messageManager->addError(__('Your password reset link has expired.'));
            $this->getResponse()->setRedirect(
                $this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl()
            );
            return;
        }

        /** @var $user \Magento\User\Model\User */
        $user = $this->_userFactory->create()->load($userId);
        $user->setPassword($password);
        $user->setPasswordConfirmation($passwordConfirmation);
        // Empty current reset password token i.e. invalidate it
        $user->setRpToken(null);
        $user->setRpTokenCreatedAt(null);
        try {
            $user->save();
            $this->messageManager->addSuccess(__('Your password has been updated.'));
            $this->getResponse()->setRedirect(
                $this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl()
            );
        } catch (\Magento\Framework\Model\Exception $exception) {
            $this->messageManager->addMessages($exception->getMessages());
            $this->_redirect(
                'adminhtml/auth/resetpassword',
                array('_nosecret' => true, '_query' => array('id' => $userId, 'token' => $passwordResetToken))
            );
        }
    }
}
