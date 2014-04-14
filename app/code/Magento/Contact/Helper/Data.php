<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Contact\Helper;

use Magento\Customer\Service\V1\Data\Customer;
use Magento\Customer\Helper\View as CustomerViewHelper;

/**
 * Contact base helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'contact/contact/enabled';

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerViewHelper $customerViewHelper
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        CustomerViewHelper $customerViewHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_customerViewHelper = $customerViewHelper;
        parent::__construct($context);
    }

    /**
     * Check if enabled
     *
     * @return string|null
     */
    public function isEnabled()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var Customer $customer
         */
        $customer = $this->_customerSession->getCustomerDataObject();
        return trim($this->_customerViewHelper->getCustomerName($customer));
    }

    /**
     * Get user email
     *
     * @return string
     */
    public function getUserEmail()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var Customer $customer
         */
        $customer = $this->_customerSession->getCustomerDataObject();
        return $customer->getEmail();
    }
}
