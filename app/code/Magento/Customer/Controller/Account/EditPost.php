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
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\AuthenticationException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditPost extends \Magento\Customer\Controller\Account
{
    /** @var CustomerAccountServiceInterface  */
    protected $customerAccountService;

    /** @var \Magento\Customer\Model\CustomerExtractor */
    protected $customerExtractor;

    /** @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder */
    protected $_customerDetailsBuilder;

    /** @var \Magento\Core\App\Action\FormKeyValidator */
    protected $_formKeyValidator;

    /** @var \Magento\Customer\Service\V1\Data\CustomerBuilder */
    protected $_customerBuilder;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
     * @param \Magento\Customer\Model\CustomerExtractor $customerExtractor
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder,
        \Magento\Customer\Model\CustomerExtractor $customerExtractor
    ) {
        $this->customerAccountService = $customerAccountService;
        $this->_customerDetailsBuilder = $customerDetailsBuilder;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_customerBuilder = $customerBuilder;
        $this->customerExtractor = $customerExtractor;
        parent::__construct($context, $customerSession);
    }

    /**
     * Change customer password action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->_redirect('*/*/edit');
            return;
        }

        if ($this->getRequest()->isPost()) {
            $customerId = $this->_getSession()->getCustomerId();
            $customer = $this->customerExtractor->extract('customer_account_edit', $this->_request);
            $this->_customerBuilder->populate($customer);
            $this->_customerBuilder->setId($customerId);
            $customer = $this->_customerBuilder->create();

            if ($this->getRequest()->getParam('change_password')) {
                $currPass = $this->getRequest()->getPost('current_password');
                $newPass = $this->getRequest()->getPost('password');
                $confPass = $this->getRequest()->getPost('confirmation');

                if (strlen($newPass)) {
                    if ($newPass == $confPass) {
                        try {
                            $this->customerAccountService->changePassword($customerId, $currPass, $newPass);
                        } catch (AuthenticationException $e) {
                            $this->messageManager->addError($e->getMessage());
                        } catch (\Exception $e) {
                            $this->messageManager->addException(
                                $e,
                                __('A problem was encountered trying to change password.')
                            );
                        }
                    } else {
                        $this->messageManager->addError(__('Confirm your new password'));
                    }
                } else {
                    $this->messageManager->addError(__('New password field cannot be empty.'));
                }
            }

            try {
                $this->_customerDetailsBuilder->setCustomer($customer);
                $this->customerAccountService->updateCustomer($customerId, $this->_customerDetailsBuilder->create());
            } catch (AuthenticationException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (InputException $e) {
                $this->messageManager->addException($e, __('Invalid input'));
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Cannot save the customer.') . $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>'
                );
            }

            if ($this->messageManager->getMessages()->getCount() > 0) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit');
                return;
            }

            $this->messageManager->addSuccess(__('The account information has been saved.'));
            $this->_redirect('customer/account');
            return;
        }

        $this->_redirect('*/*/edit');
    }
}
