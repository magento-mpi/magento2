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
     * Check whether reminder rules should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_storeConfig->getValue(self::XML_PATH_ENABLED, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }

    /**
     * Return maximum emails that can be send per one run
     *
     * @return int
     */
    public function getOneRunLimit()
    {
        return (int)$this->_storeConfig->getValue(self::XML_PATH_SEND_LIMIT, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }

    /**
     * Return email sender information
     *
     * @return string
     */
    public function getEmailIdentity()
    {
        return (string)$this->_storeConfig->getValue(self::XML_PATH_EMAIL_IDENTITY, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }

    /**
     * Return email send failure threshold
     *
     * @return int
     */
    public function getSendFailureThreshold()
    {
        return (int)$this->_storeConfig->getValue(self::XML_PATH_EMAIL_THRESHOLD, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }
}
