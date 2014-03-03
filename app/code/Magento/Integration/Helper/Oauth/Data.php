<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Integration\Helper\Oauth;

/**
 * OAuth View Helper for Controllers
 */
class Data
{
    /** @var \Magento\Core\Model\Store\Config */
    protected $_storeConfig;

    /**
     * @param \Magento\Core\Model\Store\Config $storeConfig
     */
    public function __construct(\Magento\Core\Model\Store\Config $storeConfig)
    {
        $this->_storeConfig = $storeConfig;
    }

    /**#@+
     * Cleanup xpath config settings
     */
    const XML_PATH_CLEANUP_PROBABILITY = 'oauth/cleanup/cleanup_probability';
    const XML_PATH_CLEANUP_EXPIRATION_PERIOD = 'oauth/cleanup/expiration_period';
    /**#@-*/

    /**
     * Cleanup expiration period in minutes
     */
    const CLEANUP_EXPIRATION_PERIOD_DEFAULT = 120;

    /**#@+
     * Consumer xpath settings
     */
    const XML_PATH_CONSUMER_EXPIRATION_PERIOD = 'oauth/consumer/expiration_period';
    const XML_PATH_CONSUMER_POST_MAXREDIRECTS = 'oauth/consumer/post_maxredirects';
    const XML_PATH_CONSUMER_POST_TIMEOUT = 'oauth/consumer/post_timeout';
    /**#@-*/

    /**#@+
     * Consumer default settings
     */
    const CONSUMER_EXPIRATION_PERIOD_DEFAULT = 300;
    const CONSUMER_POST_TIMEOUT_DEFAULT = 5;
    /**#@-*/

    /**
     * Calculate cleanup possibility for data with lifetime property
     *
     * @return bool
     */
    public function isCleanupProbability()
    {
        // Safe get cleanup probability value from system configuration
        $configValue = (int) $this->_storeConfig->getConfig(self::XML_PATH_CLEANUP_PROBABILITY);
        return $configValue > 0 ? 1 == mt_rand(1, $configValue) : false;
    }

    /**
     * Get cleanup expiration period value from system configuration in minutes
     *
     * @return int
     */
    public function getCleanupExpirationPeriod()
    {
        $minutes = (int) $this->_storeConfig->getConfig(self::XML_PATH_CLEANUP_EXPIRATION_PERIOD);
        return $minutes > 0 ? $minutes : self::CLEANUP_EXPIRATION_PERIOD_DEFAULT;
    }

    /**
     * Get consumer expiration period value from system configuration in seconds
     *
     * @return int
     */
    public function getConsumerExpirationPeriod()
    {
        $seconds = (int)$this->_storeConfig->getConfig(self::XML_PATH_CONSUMER_EXPIRATION_PERIOD);
        return $seconds > 0 ? $seconds : self::CONSUMER_EXPIRATION_PERIOD_DEFAULT;
    }

    /**
     * Get the number of consumer post maximum redirects
     *
     * @return int
     */
    public function getConsumerPostMaxRedirects()
    {
        $redirects = (int)$this->_storeConfig->getConfig(self::XML_PATH_CONSUMER_POST_MAXREDIRECTS);
        return $redirects > 0 ? $redirects : 0;
    }

    /**
     * Get the number seconds for the consumer post timeout
     *
     * @return int
     */
    public function getConsumerPostTimeout()
    {
        $seconds = (int)$this->_storeConfig->getConfig(self::XML_PATH_CONSUMER_POST_TIMEOUT);
        return $seconds > 0 ? $seconds : self::CONSUMER_POST_TIMEOUT_DEFAULT;
    }
}
