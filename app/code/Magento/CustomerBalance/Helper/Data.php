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
namespace Magento\CustomerBalance\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * XML configuration paths
     */
    const XML_PATH_ENABLED     = 'customer/magento_customerbalance/is_enabled';
    const XML_PATH_AUTO_REFUND = 'customer/magento_customerbalance/refund_automatically';

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig
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
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_ENABLED) == 1;
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
