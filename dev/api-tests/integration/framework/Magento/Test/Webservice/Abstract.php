<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base abstract class for webservice client test adapters
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
abstract class Magento_Test_Webservice_Abstract
{
    /**
     * Session ID
     *
     * @var string
     */
    protected $_session;

    /**
     * Soap client adapter
     *
     * @var mixed
     */
    protected $_client;

    /**
     * Webservice full URL
     *
     * @var string
     */
    protected $_url;

    /**
     * Webservice URL path
     *
     * @var string
     */
    protected $_urlPath = '';

    /**
     * Webservice client base init method
     *
     * @abstract
     * @return Magento_Test_Webservice_Abstract
     */
    abstract public function init();

    /**
     * Webservice client base call method
     *
     * @abstract
     * @param string $path
     * @param array $params
     * @return string|array
     */
    abstract public function call($path, $params = array());

    /**
     * Login to API
     *
     * @param string $api
     * @param string $key
     * @return string
     */
    public function login($api, $key)
    {
        return $this->call('login', array($api, $key));
    }

    /**
     * Check if login to API was successful
     *
     * @return bool
     */
    public function hasSession()
    {
        return !empty($this->_session);
    }

    /**
     * Set session ID
     *
     * @param string $sessionId
     * @return Magento_Test_Webservice_Abstract
     */
    public function setSession($sessionId)
    {
        $this->_session = $sessionId;
        return $this;
    }

    /**
     * Get session ID
     *
     * @return string|null
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Get Soap Client adapter
     *
     * @return null|Zend_Soap_Client
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Get last response
     *
     * @return string
     */
    public function getLastResponse()
    {
        return $this->getClient()->getLastResponse();
    }

    /**
     * Get exception class name
     *
     * @abstract
     * @return string
     */
    abstract public function getExceptionClass();

    /**
     * Get client URL
     *
     * @return string
     */
    public function getClientUrl()
    {
        if (null === $this->_url) {
            $this->_url = rtrim(TESTS_WEBSERVICE_URL, '/') . '/' . ltrim($this->_urlPath, '/');
        }
        return $this->_url;
    }

    /**
     * Get status of showing bad response
     *
     * @return string
     */
    protected function _isShowInvalidResponse()
    {
        return TESTS_WEBSERVICE_SHOW_INVALID_RESPONSE;
    }
}
