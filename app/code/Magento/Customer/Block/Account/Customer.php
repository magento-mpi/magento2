<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;

use Magento\Customer\Model\Context;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

class Customer extends \Magento\Framework\View\Element\Template
{
    /** @var CustomerAccountServiceInterface */
    protected $_customerAccountService;

    /** @var \Magento\Customer\Helper\View */
    protected $_viewHelper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
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
        return (bool)$this->httpContext->getValue(Context::CONTEXT_AUTH);
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
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }
}
