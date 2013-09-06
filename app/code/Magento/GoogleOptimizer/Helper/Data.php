<?php
/**
 * Google Optimizer Data Helper
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Helper_Data extends Magento_Core_Helper_Abstract
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
     * @var Magento_Core_Model_Store_ConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var Magento_GoogleAnalytics_Helper_Data
     */
    protected $_analyticsHelper;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_ConfigInterface $storeConfig
     * @param Magento_GoogleAnalytics_Helper_Data $analyticsHelper
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_ConfigInterface $storeConfig,
        Magento_GoogleAnalytics_Helper_Data $analyticsHelper
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
        return (bool)$this->_storeConfig->getConfigFlag(self::XML_PATH_ENABLED, $store);
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
