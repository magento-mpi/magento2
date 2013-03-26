<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_AdminNotification_Block_Security extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Varien_Http_Adapter_CurlFactory
     */
    protected $_curlFactory;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Config $config
     * @param Varien_Http_Adapter_CurlFactory $curlFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Config $config,
        Varien_Http_Adapter_CurlFactory $curlFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_config = $config;
        $this->_curlFactory = $curlFactory;
    }


    // Cache kay for saving verification result
    const VERIFICATION_RESULT_CACHE_KEY = 'configuration_files_access_level_verification';

    /**
     * File path for verification
     * @var string
     */
    private $_filePath = 'app/etc/local.xml';

    /**
     * Time out for HTTP verification request
     * @var int
     */
    private $_verificationTimeOut  = 2;

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
        $adminSessionLifetime = (int)$this->_storeConfig->getConfig('admin/security/session_lifetime');
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
        $defaultUnsecureBaseURL = (string) $this->_config
            ->getNode('default/' . Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);

        $http = $this->_curlFactory->create();
        $http->setConfig(array('timeout' => $this->_verificationTimeOut));
        $http->write(Zend_Http_Client::POST, $defaultUnsecureBaseURL . $this->_filePath);
        $responseBody = $http->read();
        $responseCode = Zend_Http_Response::extractCode($responseBody);
        $http->close();

        return $responseCode == 200;
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_canShowNotification()) {
            return '';
        }
        return parent::_toHtml();
    }
}
