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
class Magento_Contacts_Helper_Data extends Magento_Core_Helper_Abstract
{

    const XML_PATH_ENABLED   = 'contacts/contacts/enabled';

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Customer_Model_Session $customerSession
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Customer_Model_Session $customerSession
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    public function isEnabled()
    {
        return $this->_coreStoreConfig->getConfig( self::XML_PATH_ENABLED );
    }

    public function getUserName()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        $customer = $this->_customerSession->getCustomer();
        return trim($customer->getName());
    }

    public function getUserEmail()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        $customer = $this->_customerSession->getCustomer();
        return $customer->getEmail();
    }
}
