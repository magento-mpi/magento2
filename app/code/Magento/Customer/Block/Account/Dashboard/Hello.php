<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account\Dashboard;

class Hello extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_viewHelper;

    /**
     * @var \Magento\Customer\Service\V1\CustomerServiceInterface
     */
    protected $_customerService;

    /**
     * Constructor
     *
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_viewHelper = $viewHelper;
        $this->_customerService = $customerService;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Concatenate all customer name parts into full customer name.
     *
     * @return string
     */
    public function getCustomerName()
    {
        $customer = $this->_customerService->getCustomer($this->_customerSession->getCustomerId());
        return $this->_viewHelper->getCustomerName($customer);
    }
}
