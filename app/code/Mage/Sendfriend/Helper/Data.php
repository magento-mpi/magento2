<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sendfriend Data Helper
 *
 * @category    Mage
 * @package     Mage_Sedfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sendfriend_Helper_Data extends Magento_Core_Helper_Abstract
{
    const XML_PATH_ENABLED          = 'sendfriend/email/enabled';
    const XML_PATH_ALLOW_FOR_GUEST  = 'sendfriend/email/allow_guest';
    const XML_PATH_MAX_RECIPIENTS   = 'sendfriend/email/max_recipients';
    const XML_PATH_MAX_PER_HOUR     = 'sendfriend/email/max_per_hour';
    const XML_PATH_LIMIT_BY         = 'sendfriend/email/check_by';
    const XML_PATH_EMAIL_TEMPLATE   = 'sendfriend/email/template';

    const COOKIE_NAME   = 'stf';

    const CHECK_IP      = 1;
    const CHECK_COOKIE  = 0;

    /**
     * Check is enabled Module
     *
     * @param int $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Check allow send email for guest
     *
     * @param int $store
     * @return bool
     */
    public function isAllowForGuest($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ALLOW_FOR_GUEST, $store);
    }

    /**
     * Retrieve Max Recipients
     *
     * @param int $store
     * @return int
     */
    public function getMaxRecipients($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_MAX_RECIPIENTS, $store);
    }

    /**
     * Retrieve Max Products Sent in 1 Hour
     *
     * @param int $store
     * @return int
     */
    public function getMaxEmailPerPeriod($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_MAX_PER_HOUR, $store);
    }

    /**
     * Retrieve Limitation Period in seconds (1 hour)
     *
     * @return int
     */
    public function getPeriod()
    {
        return 3600;
    }

    /**
     * Retrieve Limit Sending By
     *
     * @param int $store
     * @return int
     */
    public function getLimitBy($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_LIMIT_BY, $store);
    }

    /**
     * Retrieve Email Template
     *
     * @param int $store
     * @return mixed
     */
    public function getEmailTemplate($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $store);
    }

    /**
     * Retrieve Key Name for Cookie
     *
     * @see self::COOKIE_NAME
     * @return string
     */
    public function getCookieName()
    {
        return self::COOKIE_NAME;
    }
}
