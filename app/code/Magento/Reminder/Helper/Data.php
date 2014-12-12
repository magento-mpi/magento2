<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reminder\Helper;

/**
 * Reminder rules data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'promo/magento_reminder/enabled';

    const XML_PATH_SEND_LIMIT = 'promo/magento_reminder/limit';

    const XML_PATH_EMAIL_IDENTITY = 'promo/magento_reminder/identity';

    const XML_PATH_EMAIL_THRESHOLD = 'promo/magento_reminder/threshold';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Check whether reminder rules should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_scopeConfig->getValue(self::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return maximum emails that can be send per one run
     *
     * @return int
     */
    public function getOneRunLimit()
    {
        return (int)$this->_scopeConfig->getValue(self::XML_PATH_SEND_LIMIT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return email sender information
     *
     * @return string
     */
    public function getEmailIdentity()
    {
        return (string)$this->_scopeConfig->getValue(self::XML_PATH_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return email send failure threshold
     *
     * @return int
     */
    public function getSendFailureThreshold()
    {
        return (int)$this->_scopeConfig->getValue(self::XML_PATH_EMAIL_THRESHOLD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
