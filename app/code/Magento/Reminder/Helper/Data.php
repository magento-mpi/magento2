<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Helper;

/**
 * Reminder rules data helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'promo/magento_reminder/enabled';
    const XML_PATH_SEND_LIMIT = 'promo/magento_reminder/limit';
    const XML_PATH_EMAIL_IDENTITY = 'promo/magento_reminder/identity';
    const XML_PATH_EMAIL_THRESHOLD = 'promo/magento_reminder/threshold';

    /**
     * Core store config
     *
     * @var \Magento\Store\Model\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Store\Model\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Store\Model\Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Check whether reminder rules should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_coreStoreConfig->getConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Return maximum emails that can be send per one run
     *
     * @return int
     */
    public function getOneRunLimit()
    {
        return (int)$this->_coreStoreConfig->getConfig(self::XML_PATH_SEND_LIMIT);
    }

    /**
     * Return email sender information
     *
     * @return string
     */
    public function getEmailIdentity()
    {
        return (string)$this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_IDENTITY);
    }

    /**
     * Return email send failure threshold
     *
     * @return int
     */
    public function getSendFailureThreshold()
    {
        return (int)$this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_THRESHOLD);
    }
}
