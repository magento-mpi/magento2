<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller;

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
     * @param CustomerAccountService $customerAccountService
     * @param CustomerMetadataService $customerMetadataService
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerAccountService $customerAccountService,
        CustomerMetadataService $customerMetadataService
    ) {
        $this->_customerSession = $customerSession;
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
            $customerDetails = $this->_customerAccountService->getCustomerDetails($customerId);
        } catch (NoSuchEntityException $e) {
            return true;
        }

        if (isset($customerDetails)) {
            $validationResult = $this->_customerAccountService->validateCustomerDetails(
                $customerDetails,
                $this->_customerMetadataService->getAllCustomerAttributeMetadata()
            );
            if ((true !== $validationResult) && is_array($validationResult)) {
                if ($addErrors) {
                    $this->messageManager->addError($validationResult['message']);
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
