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
     * @var \Magento\Store\Model\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Store\Model\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Store\Model\Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
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
        $accountId = $this->_coreStoreConfig->getValue(self::XML_PATH_ACCOUNT, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
        return $accountId && $this->_coreStoreConfig->isSetFlag(self::XML_PATH_ACTIVE, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
    }
}
