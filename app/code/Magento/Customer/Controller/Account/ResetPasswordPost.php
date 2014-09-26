<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Account;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

class ResetPasswordPost extends \Magento\Customer\Controller\Account
{
    /** @var CustomerAccountServiceInterface  */
    protected $customerAccountService;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerAccountServiceInterface $customerAccountService
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerAccountServiceInterface $customerAccountService
    ) {
        $this->customerAccountService = $customerAccountService;
        parent::__construct($context, $customerSession);
    }

    /**
     * Reset forgotten password
     *
     * Used to handle data received from reset forgotten password form
     *
     * @return void
     */
    public function execute()
    {
        $resetPasswordToken = (string)$this->getRequest()->getQuery('token');
        $customerId = (int)$this->getRequest()->getQuery('id');
        $password = (string)$this->getRequest()->getPost('password');
        $passwordConfirmation = (string)$this->getRequest()->getPost('confirmation');

        if ($password !== $passwordConfirmation) {
            $this->messageManager->addError(__("New Password and Confirm New Password values didn't match."));
            return;
        }
        if (iconv_strlen($password) <= 0) {
            $this->messageManager->addError(__('New password field cannot be empty.'));
            $this->_redirect('*/*/createPassword', array('id' => $customerId, 'token' => $resetPasswordToken));
            return;
        }

        try {
            $this->customerAccountService->resetPassword($customerId, $resetPasswordToken, $password);
            $this->messageManager->addSuccess(__('Your password has been updated.'));
            $this->_redirect('*/*/login');
            return;
        } catch (\Exception $exception) {
            $this->messageManager->addError(__('There was an error saving the new password.'));
            $this->_redirect('*/*/createPassword', array('id' => $customerId, 'token' => $resetPasswordToken));
            return;
        }
    }
}
