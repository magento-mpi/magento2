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
 * Webservice XML-RPC adapter
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 * @var _test Magento_Test_Webservice_XmlRpc
 */
class Magento_Test_Webservice_XmlRpc extends Magento_Test_Webservice_Abstract
{
    /**
     * Class of exception web services client throws
     */
    const EXCEPTION_CLASS = 'Zend_XmlRpc_Client_FaultException';

    /**
     * HTTP client options
     *
     * @var array
     */
    protected $_httpClientOptions = array('timeout' => 60);

    /**
     * URL path
     *
     * @var string
     */
    protected $_urlPath = '/api/xmlrpc/';

    /**
     * XML-RPC client adapter
     *
     * @var Zend_XmlRpc_Client
     */
    protected $_client;

    /**
     * Initialize
     *
     * @param array|null $options
     * @return Magento_Test_Webservice_XmlRpc
     */
    public function init($options = null)
    {
        $this->_client = new Zend_XmlRpc_Client($this->getClientUrl($options));
        // 30 seconds wasn't enough for some crud tests, increased to timeout 60
        $this->_client->getHttpClient()->setConfig($this->_httpClientOptions);
        $apiUser = isset($options['api_user']) ? $options['api_user'] : TESTS_WEBSERVICE_USER;
        $apiKey  = isset($options['api_key']) ? $options['api_key'] : TESTS_WEBSERVICE_APIKEY;
        $this->setSession($this->_client->call('login', array($apiKey, $apiUser)));
        return $this;
    }

    /**
     * Webservice client call method
     *
     * @param string $path
     * @param array $params
     * @return string|array
     * @throws Magento_Test_Webservice_Exception|Zend_XmlRpc_Client_FaultException
     */
    public function call($path, $params = array())
    {
        try {
            return $this->_client->call('call', array($this->_session, $path, $params));
        } catch (Zend_XmlRpc_Client_FaultException $e) {
            $this->_throwExceptionBadRequest($e);
            throw $e;
        }
    }

    /**
     * Login with credentials
     *
     * @param $username
     * @param $apiKey
     * @return string
     * @throws Zend_XmlRpc_Client_FaultException
     */
    public function login($username, $apiKey)
    {
        try {
            return $this->_client->call('login', array($username, $apiKey));
        } catch (Zend_XmlRpc_Client_FaultException $e) {
            $this->_throwExceptionBadRequest($e);
            throw $e;
        }
    }

    /**
     * Try to throw exception with show response
     *
     * @param Zend_XmlRpc_Client_FaultException $e
     * @return Magento_Test_Webservice_XmlRpc
     * @throws Magento_Test_Webservice_Exception
     */
    protected function _throwExceptionBadRequest(Zend_XmlRpc_Client_FaultException $e)
    {
        if ($this->_isShowInvalidResponse()) {
            $message = $e->getMessage();
            if ('Failed to parse response' == $message || 'Invalid response' == $message) {
                throw new Magento_Test_Webservice_Exception(sprintf(
                    'XML-RPC should be get XML document but got following: "%s"',
                    $this->getLastResponse()));
            }
        }
        return $this;
    }

    /**
     * Give web service client exception class
     *
     * @return string
     */
    public function getExceptionClass()
    {
        return self::EXCEPTION_CLASS;
    }

    /**
     * Get content of last response from HTTP client
     *
     * @return string
     */
    public function getLastResponse()
    {
        return $this->_client->getHttpClient()->getLastResponse()->getBody();
    }
}
