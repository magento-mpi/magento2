<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleAnalytics
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GoogleAnalytics data helper
 *
 * @category   Magento
 * @package    Magento_GoogleAnalytics
 */
class Magento_GoogleAnalytics_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE  = 'google/analytics/active';
    const XML_PATH_ACCOUNT = 'google/analytics/account';

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Whether GA is ready to use
     *
     * @param mixed $store
     * @return bool
     */
    public function isGoogleAnalyticsAvailable($store = null)
    {
        $accountId = Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
        return $accountId && $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_ACTIVE, $store);
    }
}
