<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller;

use Magento\Customer\Service\V1\CustomerServiceInterface as CustomerService;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface as CustomerAccountService;
use Magento\Customer\Service\V1\CustomerMetadataServiceInterface as CustomerMetadataService;
use Magento\Exception\NoSuchEntityException;

/**
 * Controller for onepage checkouts
 */
abstract class Action extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var CustomerService
     */
    protected $_customerService;

    /**
     * @var CustomerAccountService
     */
    protected $_customerAccountService;

    /**
     * @var CustomerMetadataService
     */
    protected $_customerMetadataService;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerService $customerService
     * @param CustomerAccountService $customerAccountService
     * @param CustomerMetadataService $customerMetadataService
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerService $customerService,
        CustomerAccountService $customerAccountService,
        CustomerMetadataService $customerMetadataService
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerService = $customerService;
        $this->_customerAccountService = $customerAccountService;
        $this->_customerMetadataService = $customerMetadataService;
        parent::__construct($context);
    }

    /**
     * Make sure customer is valid, if logged in
     *
     * By default will add error messages and redirect to customer edit form
     *
     * @param bool $redirect - stop dispatch and redirect?
     * @param bool $addErrors - add error messages?
     * @return bool
     */
    protected function _preDispatchValidateCustomer($redirect = true, $addErrors = true)
    {
        try {
            $customerId = $this->_customerSession->getCustomerId();
            $customer = $this->_customerService->getCustomer($customerId);
        } catch (NoSuchEntityException $e) {
            return true;
        }

        if (isset($customer)) {
            $validationResult = $this->_customerAccountService->validateCustomerData(
                $customer,
                $this->_customerMetadataService->getAllCustomerAttributeMetadata()
            );
            if ((true !== $validationResult) && is_array($validationResult)) {
                if ($addErrors) {
                    foreach ($validationResult as $error) {
                        $this->messageManager->addError($error);
                    }
                }
                if ($redirect) {
                    $this->_redirect('customer/account/edit');
                    $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
                }
                return false;
            }
        }
        return true;
    }
}
