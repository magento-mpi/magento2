<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

class Customer extends \Magento\View\Element\Template
{
    /** @var CustomerAccountServiceInterface */
    protected $_customerAccountService;

    /** @var \Magento\Customer\Helper\View */
    protected $_viewHelper;

    /**
     * @var \Magento\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentService
     */
    protected $currentCustomer;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\App\Http\Context $httpContext,
        \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_customerAccountService = $customerAccountService;
        $this->_viewHelper = $viewHelper;
        $this->httpContext = $httpContext;
        $this->currentCustomer = $currentCustomer;
        $this->_isScopePrivate = true;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function customerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH);
    }

    /**
     * Return the full name of the customer currently logged in
     *
     * @return string|null
     */
    public function getCustomerName()
    {
        try {
            $customer = $this->_customerAccountService->getCustomer($this->currentCustomer->getCustomerId());
            return $this->escapeHtml($this->_viewHelper->getCustomerName($customer));
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            return null;
        }
    }
}
