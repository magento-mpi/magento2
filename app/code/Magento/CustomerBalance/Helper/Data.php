<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customerbalance helper
 *
 */
class Magento_CustomerBalance_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * XML configuration paths
     */
    const XML_PATH_ENABLED     = 'customer/magento_customerbalance/is_enabled';
    const XML_PATH_AUTO_REFUND = 'customer/magento_customerbalance/refund_automatically';

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
     * Check whether customer balance functionality should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_ENABLED) == 1;
    }

    /**
     * Check if automatically refund is enabled
     *
     * @return bool
     */
    public function isAutoRefundEnabled()
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_AUTO_REFUND);
    }
}
