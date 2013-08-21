<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customerbalance helper
 *
 */
class Enterprise_CustomerBalance_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * XML configuration paths
     */
    const XML_PATH_ENABLED     = 'customer/enterprise_customerbalance/is_enabled';
    const XML_PATH_AUTO_REFUND = 'customer/enterprise_customerbalance/refund_automatically';

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
        return Mage::getStoreConfigFlag(self::XML_PATH_AUTO_REFUND);
    }
}
