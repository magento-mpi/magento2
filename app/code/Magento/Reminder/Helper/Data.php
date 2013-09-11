<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder rules data helper
 */
namespace Magento\Reminder\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'promo/magento_reminder/enabled';
    const XML_PATH_SEND_LIMIT = 'promo/magento_reminder/limit';
    const XML_PATH_EMAIL_IDENTITY = 'promo/magento_reminder/identity';
    const XML_PATH_EMAIL_THRESHOLD = 'promo/magento_reminder/threshold';

    /**
     * Check whether reminder rules should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)\Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Return maximum emails that can be send per one run
     *
     * @return int
     */
    public function getOneRunLimit()
    {
        return (int)\Mage::getStoreConfig(self::XML_PATH_SEND_LIMIT);
    }

    /**
     * Return email sender information
     *
     * @return string
     */
    public function getEmailIdentity()
    {
        return (string)\Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY);
    }

    /**
     * Return email send failure threshold
     *
     * @return int
     */
    public function getSendFailureThreshold()
    {
        return (int)\Mage::getStoreConfig(self::XML_PATH_EMAIL_THRESHOLD);
    }
}
