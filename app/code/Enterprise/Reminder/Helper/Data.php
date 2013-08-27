<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder rules data helper
 */
class Enterprise_Reminder_Helper_Data extends Magento_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'promo/enterprise_reminder/enabled';
    const XML_PATH_SEND_LIMIT = 'promo/enterprise_reminder/limit';
    const XML_PATH_EMAIL_IDENTITY = 'promo/enterprise_reminder/identity';
    const XML_PATH_EMAIL_THRESHOLD = 'promo/enterprise_reminder/threshold';

    /**
     * Check whether reminder rules should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Return maximum emails that can be send per one run
     *
     * @return int
     */
    public function getOneRunLimit()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_SEND_LIMIT);
    }

    /**
     * Return email sender information
     *
     * @return string
     */
    public function getEmailIdentity()
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY);
    }

    /**
     * Return email send failure threshold
     *
     * @return int
     */
    public function getSendFailureThreshold()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_EMAIL_THRESHOLD);
    }
}
