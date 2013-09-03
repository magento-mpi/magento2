<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminNotification_Model_System_Message_Security
    implements Magento_AdminNotification_Model_System_MessageInterface
{
    /**
     * Cache kay for saving verification result
     */
    const VERIFICATION_RESULT_CACHE_KEY = 'configuration_files_access_level_verification';

    /**
     * File path for verification
     *
     * @var string
     */
    private $_filePath = 'app/etc/local.xml';

    /**
     * Time out for HTTP verification request
     * @var int
     */
    private $_verificationTimeOut  = 2;

    /**
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento\HTTP\Adapter\CurlFactory
     */
    protected $_curlFactory;

    /**
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento\HTTP\Adapter\CurlFactory $curlFactory
     */
    public function __construct(
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Core_Model_Config $config,
        Magento\HTTP\Adapter\CurlFactory $curlFactory
    ) {
        $this->_cache = $cache;
        $this->_storeConfig = $storeConfig;
        $this->_config = $config;
        $this->_curlFactory = $curlFactory;
    }

    /**
     * Check verification result and return true if system must to show notification message
     *
     * @return bool
     */
    private function _canShowNotification()
    {
        if ($this->_cache->load(self::VERIFICATION_RESULT_CACHE_KEY)) {
            return false;
        }

        if ($this->_isFileAccessible()) {
            return true;
        }

        $adminSessionLifetime = (int) $this->_storeConfig->getConfig('admin/security/session_lifetime');
        $this->_cache->save(true, self::VERIFICATION_RESULT_CACHE_KEY, array(), $adminSessionLifetime);
        return false;
    }

    /**
     * If file is accessible return true or false
     *
     * @return bool
     */
    private function _isFileAccessible()
    {
        $unsecureBaseURL = (string) $this->_config->getNode(
            'default/' . Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL
        );

        /** @var $http Magento\HTTP\Adapter\Curl */
        $http = $this->_curlFactory->create();
        $http->setConfig(array('timeout' => $this->_verificationTimeOut));
        $http->write(Zend_Http_Client::POST, $unsecureBaseURL . $this->_filePath);
        $responseBody = $http->read();
        $responseCode = Zend_Http_Response::extractCode($responseBody);
        $http->close();

        return $responseCode == 200;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return 'security';
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->_canShowNotification();
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return __('Your web server is configured incorrectly. As a result, configuration files with sensitive information are accessible from the outside. Please contact your hosting provider.');
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return Magento_AdminNotification_Model_System_MessageInterface::SEVERITY_CRITICAL;
    }
}
