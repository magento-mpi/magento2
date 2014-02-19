<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account;

class Customer extends \Magento\View\Element\Template
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /** @var \Magento\Customer\Service\V1\CustomerServiceInterface */
    protected $_customerService;

    /** @var \Magento\Customer\Helper\View */
    protected $_viewHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        \Magento\Customer\Helper\View $viewHelper,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $session;
        $this->_customerService = $customerService;
        $this->_viewHelper = $viewHelper;
        $this->_isScopePrivate = true;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */

    public function customerLoggedIn()
    {
        return (bool)$this->_customerSession->isLoggedIn();
    }

    /**
     * @throws \Magento\Exception\NoSuchEntityException If customer is not found.
     * @return string
     */
    public function getCustomerName()
    {
        try {
            $customer = $this->_customerService->getCustomer($this->_customerSession->getCustomerId());
            return $this->escapeHtml($this->_viewHelper->getCustomerName($customer));
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            return null;
        }
    }
}
