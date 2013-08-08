<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleAnalytics
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GoogleAnalytics data helper
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 */
class Mage_GoogleAnalytics_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE  = 'google/analytics/active';
    const XML_PATH_ACCOUNT = 'google/analytics/account';

    /**
     * Whether GA is ready to use
     *
     * @param mixed $store
     * @return bool
     */
    public function isGoogleAnalyticsAvailable($store = null)
    {
        $accountId = Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
        return $accountId && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
    }
}
