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

    /** @var \Magento\Customer\Model\Converter */
    private $_converter;

    /** @var \Magento\Customer\Service\V1\CustomerServiceInterface */
    protected $_customerService;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Customer\Model\Converter $converter
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Model\Converter $converter,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $session;
        $this->_converter = $converter;
        $this->_customerService = $customerService;
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
            $customerModel = $this->_converter->getCustomerModel($this->_customerSession->getCustomerId());
            return $this->escapeHtml($customerModel->getName());
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            return null;
        }
    }
}
