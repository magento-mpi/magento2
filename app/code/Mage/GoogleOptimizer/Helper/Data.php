<?php
/**
 * Google Optimizer Data Helper
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Helper_Data extends Mage_Core_Helper_Abstract
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
     * @var Mage_Core_Model_Store_ConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Store_ConfigInterface $storeConfig
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Store_ConfigInterface $storeConfig
    ) {
        $this->_storeConfig = $storeConfig;
        parent::__construct($context);
    }

    /**
     * Checks if Google Experiment is active
     *
     * @param string $store
     * @return bool
     */
    public function isGoogleExperimentActive($store = null)
    {
        $googleAnalyticsActive = (bool)$this->_storeConfig->getConfigFlag(
            Mage_GoogleAnalytics_Helper_Data::XML_PATH_ACTIVE,
            $store
        );
        $googleExperimentActive = (bool)$this->_storeConfig->getConfigFlag(self::XML_PATH_ENABLED, $store);
        return $googleExperimentActive && $googleAnalyticsActive;
    }
}
