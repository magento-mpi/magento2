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

use Magento\Core\Model\Store;

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
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig
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
        $accountId = $this->_coreStoreConfig->getConfig(self::XML_PATH_ACCOUNT, $store);
        return $accountId && $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_ACTIVE, $store);
    }
}
