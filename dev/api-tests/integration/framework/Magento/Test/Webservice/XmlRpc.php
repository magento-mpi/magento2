<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * @return Magento_Test_Webservice_XmlRpc
     */
    public function init()
    {
        $this->_client = new Zend_XmlRpc_Client($this->getClientUrl());
        // 30 seconds wasn't enough for some crud tests, increased to timeout 60
        $this->_client->getHttpClient()->setConfig($this->_httpClientOptions);
        $this->setSession($this->_client->call('login',array(TESTS_WEBSERVICE_USER, TESTS_WEBSERVICE_APIKEY)));
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
