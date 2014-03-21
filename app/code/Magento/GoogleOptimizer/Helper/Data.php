<?php
/**
 * Google Optimizer Data Helper
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Helper;

class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Xml path google experiments enabled
     */
    const XML_PATH_ENABLED = 'google/analytics/experiments';

    /**
     * @var bool
     */
    protected $_activeForCmsFlag;

    /**
     * @var \Magento\Store\Model\ConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\GoogleAnalytics\Helper\Data
     */
    protected $_analyticsHelper;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Store\Model\ConfigInterface $storeConfig
     * @param \Magento\GoogleAnalytics\Helper\Data $analyticsHelper
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Store\Model\ConfigInterface $storeConfig,
        \Magento\GoogleAnalytics\Helper\Data $analyticsHelper
    ) {
        $this->_storeConfig = $storeConfig;
        $this->_analyticsHelper = $analyticsHelper;
        parent::__construct($context);
    }

    /**
     * Checks if Google Experiment is enabled
     *
     * @param string $store
     * @return bool
     */
    public function isGoogleExperimentEnabled($store = null)
    {
        return (bool)$this->_storeConfig->isSetFlag(self::XML_PATH_ENABLED, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
    }

    /**
     * Checks if Google Experiment is active
     *
     * @param string $store
     * @return bool
     */
    public function isGoogleExperimentActive($store = null)
    {
        return $this->isGoogleExperimentEnabled($store) && $this->_analyticsHelper->isGoogleAnalyticsAvailable($store);
    }
}
