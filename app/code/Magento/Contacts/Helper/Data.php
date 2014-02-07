<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Contacts
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Contacts base helper
 *
 * @category   Magento
 * @package    Magento_Contacts
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Contacts\Helper;

class Data extends \Magento\App\Helper\AbstractHelper
{

    const XML_PATH_ENABLED   = 'contacts/contacts/enabled';

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
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Check if enabled
     *
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->_coreStoreConfig->getConfig( self::XML_PATH_ENABLED );
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
        $customer = $this->_customerSession->getCustomer();
        return trim($customer->getName());
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
        $customer = $this->_customerSession->getCustomer();
        return $customer->getEmail();
    }
}
