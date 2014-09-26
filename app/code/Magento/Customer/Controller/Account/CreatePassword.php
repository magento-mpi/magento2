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

class CreatePassword extends \Magento\Customer\Controller\Account
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
     * Resetting password handler
     *
     * @return void
     */
    public function execute()
    {
        $resetPasswordToken = (string)$this->getRequest()->getParam('token');
        $customerId = (int)$this->getRequest()->getParam('id');
        try {
            $this->customerAccountService->validateResetPasswordLinkToken($customerId, $resetPasswordToken);
            $this->_view->loadLayout();
            // Pass received parameters to the reset forgotten password form
            $this->_view->getLayout()->getBlock(
                'resetPassword'
            )->setCustomerId(
                $customerId
            )->setResetPasswordLinkToken(
                $resetPasswordToken
            );
            $this->_view->renderLayout();
        } catch (\Exception $exception) {
            $this->messageManager->addError(__('Your password reset link has expired.'));
            $this->_redirect('*/*/forgotpassword');
        }
    }
}
