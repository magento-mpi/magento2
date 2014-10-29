<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Api\Data\CustomerDataBuilder;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;
use Magento\Core\App\Action\FormKeyValidator;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\AuthenticationException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditPost extends \Magento\Customer\Controller\Account
{
    /** @var CustomerAccountServiceInterface  */
    protected $customerAccountService;

    /** @var CustomerDataBuilder */
    protected $customerBuilder;

    /** @var CustomerDetailsBuilder */
    protected $customerDetailsBuilder;

    /** @var FormKeyValidator */
    protected $formKeyValidator;

    /** @var CustomerExtractor */
    protected $customerExtractor;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param CustomerDetailsBuilder $customerDetailsBuilder
     * @param FormKeyValidator $formKeyValidator
     * @param CustomerDataBuilder $customerBuilder
     * @param CustomerExtractor $customerExtractor
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerAccountServiceInterface $customerAccountService,
        CustomerDataBuilder $customerBuilder,
        CustomerDetailsBuilder $customerDetailsBuilder,
        FormKeyValidator $formKeyValidator,
        CustomerExtractor $customerExtractor
    ) {
        $this->customerAccountService = $customerAccountService;
        $this->customerBuilder = $customerBuilder;
        $this->customerDetailsBuilder = $customerDetailsBuilder;
        $this->formKeyValidator = $formKeyValidator;
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
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->_redirect('*/*/edit');
            return;
        }

        if ($this->getRequest()->isPost()) {
            $customerId = $this->_getSession()->getCustomerId();
            $customer = $this->customerExtractor->extract('customer_account_edit', $this->_request);
            $this->customerBuilder->populate($customer);
            $this->customerBuilder->setId($customerId);
            $customer = $this->customerBuilder->create();

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
                $this->customerDetailsBuilder->setCustomer($customer);
                $this->customerAccountService->updateCustomer($customerId, $this->customerDetailsBuilder->create());
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
