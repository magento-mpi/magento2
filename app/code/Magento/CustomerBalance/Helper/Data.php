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

class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * XML configuration paths
     */
    const XML_PATH_ENABLED     = 'customer/magento_customerbalance/is_enabled';
    const XML_PATH_AUTO_REFUND = 'customer/magento_customerbalance/refund_automatically';

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
     * Check whether customer balance functionality should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_storeConfig->getValue(self::XML_PATH_ENABLED, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE) == 1;
    }

    /**
     * Check if automatically refund is enabled
     *
     * @return bool
     */
    public function isAutoRefundEnabled()
    {
        return $this->_storeConfig->isSetFlag(self::XML_PATH_AUTO_REFUND, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }
}
