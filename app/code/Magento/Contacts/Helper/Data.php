<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Contacts\Helper;

use Magento\Customer\Service\V1\Data\Customer;
use Magento\Customer\Helper\View as ViewHelper;

/**
 * Contacts base helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'contacts/contacts/enabled';

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_viewHelper;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ViewHelper $viewHelper
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Customer\Model\Session $customerSession,
        ViewHelper $viewHelper
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_customerSession = $customerSession;
        $this->_viewHelper = $viewHelper;
        parent::__construct($context);
    }

    /**
     * Check if enabled
     *
     * @return string|null
     */
    public function isEnabled()
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_ENABLED);
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
        return trim($this->_viewHelper->getCustomerName($customer));
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
