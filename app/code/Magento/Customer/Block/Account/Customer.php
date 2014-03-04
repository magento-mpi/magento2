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

    /**
     * @var \Magento\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\App\Http\Context $httpContext,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $session;
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
     * @return string
     */
    public function getCustomerName()
    {
        return $this->escapeHtml($this->_customerSession->getCustomer()->getName());
    }
}
