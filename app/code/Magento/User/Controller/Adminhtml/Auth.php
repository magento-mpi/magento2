<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_User Auth controller
 */
class Magento_User_Controller_Adminhtml_Auth extends Magento_Backend_Controller_ActionAbstract
{
    /**
     * User model factory
     *
     * @var Magento_User_Model_UserFactory
     */
    protected $_userFactory;

    /**
     * Construct
     *
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_User_Model_UserFactory $userFactory
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_User_Model_UserFactory $userFactory
    ) {
        parent::__construct($context);
        $this->_userFactory = $userFactory;
    }

    /**
     * Forgot administrator password action
     */
    public function forgotpasswordAction()
    {
        $email = (string) $this->getRequest()->getParam('email');
        $params = $this->getRequest()->getParams();

        if (!empty($email) && !empty($params)) {
            // Validate received data to be an email address
            if (Zend_Validate::is($email, 'EmailAddress')) {
                $collection = Mage::getResourceModel('Magento_User_Model_Resource_User_Collection');
                /** @var $collection Magento_User_Model_Resource_User_Collection */
                $collection->addFieldToFilter('email', $email);
                $collection->load(false);

                if ($collection->getSize() > 0) {
                    foreach ($collection as $item) {
                        /** @var Magento_User_Model_User $user */
                        $user = $this->_userFactory->create()->load($item->getId());
                        if ($user->getId()) {
                            $newPassResetToken = $this->_objectManager->get('Magento_User_Helper_Data')
                                ->generateResetPasswordLinkToken();
                            $user->changeResetPasswordLinkToken($newPassResetToken);
                            $user->save();
                            $user->sendPasswordResetConfirmationEmail();
                        }
                        break;
                    }
                }
                // @codingStandardsIgnoreStart
                $this->_getSession()
                    ->addSuccess(__('If there is an account associated with %1 you will receive an email with a link to reset your password.', $this->_objectManager->get('Magento_User_Helper_Data')->escapeHtml($email)));
                // @codingStandardsIgnoreEnd
                $this->getResponse()->setRedirect(
                    $this->_objectManager->get('Magento_Backend_Helper_Data')->getHomePageUrl()
                );
                return;
            } else {
                $this->_getSession()->addError(__('Please correct this email address:'));
            }
        } elseif (!empty($params)) {
            $this->_getSession()->addError(__('The email address is empty.'));
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Display reset forgotten password form
     *
     * User is redirected on this action when he clicks on the corresponding link in password reset confirmation email
     */
    public function resetPasswordAction()
    {
        $passwordResetToken = (string) $this->getRequest()->getQuery('token');
        $userId = (int) $this->getRequest()->getQuery('id');
        try {
            $this->_validateResetPasswordLinkToken($userId, $passwordResetToken);

            $this->loadLayout();

            $content = $this->getLayout()->getBlock('content');
            if ($content) {
                $content->setData('user_id', $userId)
                    ->setData('reset_password_link_token', $passwordResetToken);
            }

            $this->renderLayout();
        } catch (Exception $exception) {
            $this->_getSession()->addError(
                __('Your password reset link has expired.')
            );
            $this->_redirect('*/auth/forgotpassword', array('_nosecret' => true));
            return;
        }
    }

    /**
     * Reset forgotten password
     *
     * Used to handle data received from reset forgotten password form
     */
    public function resetPasswordPostAction()
    {
        $passwordResetToken = (string) $this->getRequest()->getQuery('token');
        $userId = (int) $this->getRequest()->getQuery('id');
        $password = (string) $this->getRequest()->getPost('password');
        $passwordConfirmation = (string) $this->getRequest()->getPost('confirmation');

        try {
            $this->_validateResetPasswordLinkToken($userId, $passwordResetToken);
        } catch (Exception $exception) {
            $this->_getSession()->addError(
                __('Your password reset link has expired.')
            );
            $this->getResponse()->setRedirect(
                $this->_objectManager->get('Magento_Backend_Helper_Data')->getHomePageUrl()
            );
            return;
        }

        /** @var $user Magento_User_Model_User */
        $user = $this->_userFactory->create()->load($userId);
        if ($password !== '') {
            $user->setPassword($password);
        }
        if ($passwordConfirmation !== '') {
            $user->setPasswordConfirmation($passwordConfirmation);
        }
        // Empty current reset password token i.e. invalidate it
        $user->setRpToken(null);
        $user->setRpTokenCreatedAt(null);
        try {
            $user->save();
            $this->_getSession()->addSuccess(
                __('Your password has been updated.')
            );
            $this->getResponse()->setRedirect(
                $this->_objectManager->get('Magento_Backend_Helper_Data')->getHomePageUrl()
            );
        } catch (Magento_Core_Exception $exception) {
            $this->_getSession()->addMessages($exception->getMessages());
            $this->_redirect('*/auth/resetpassword', array(
                '_nosecret' => true,
                '_query' => array(
                    'id' => $userId,
                    'token' => $passwordResetToken
                )
            ));
        }
    }

    /**
     * Check if password reset token is valid
     *
     * @param int $userId
     * @param string $resetPasswordToken
     * @throws Magento_Core_Exception
     */
    protected function _validateResetPasswordLinkToken($userId, $resetPasswordToken)
    {
        if (!is_int($userId)
            || !is_string($resetPasswordToken)
            || empty($resetPasswordToken)
            || empty($userId)
            || $userId < 0
        ) {
            throw new Magento_Core_Exception(__('Please correct the password reset token.'));
        }

        /** @var $user Magento_User_Model_User */
        $user = $this->_userFactory->create()->load($userId);
        if (!$user->getId()) {
            throw new Magento_Core_Exception(__('Please specify the correct account and try again.'));
        }

        $userToken = $user->getRpToken();
        if (strcmp($userToken, $resetPasswordToken) != 0 || $user->isResetPasswordLinkTokenExpired()) {
            throw new Magento_Core_Exception(__('Your password reset link has expired.'));
        }
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
}
