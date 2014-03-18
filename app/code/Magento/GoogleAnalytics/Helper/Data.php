<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleAnalytics
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleAnalytics\Helper;

use Magento\Store\Model\Store;

/**
 * GoogleAnalytics data helper
 *
 * @category   Magento
 * @package    Magento_GoogleAnalytics
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE  = 'google/analytics/active';
    const XML_PATH_ACCOUNT = 'google/analytics/account';

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
    ) {
        $this->_storeConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Whether GA is ready to use
     *
     * @param null|string|bool|int|Store $store
     * @return bool
     */
    public function isGoogleAnalyticsAvailable($store = null)
    {
        $accountId = $this->_storeConfig->getValue(self::XML_PATH_ACCOUNT, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
        return $accountId && $this->_storeConfig->isSetFlag(self::XML_PATH_ACTIVE, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
    }
}
