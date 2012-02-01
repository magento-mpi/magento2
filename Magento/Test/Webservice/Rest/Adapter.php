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

class Magento_Test_Webservice_Rest_Adapter
{
    /**
     * Class of exception web services client throws
     *
     * @const
     */
    const EXCEPTION_CLASS = 'Zend_Rest_Exception';

    /**#@+
     * REST API constants
     */
    const API_VERSION = 1;
    const CONTENT_TYPE = 'application/json';
    const ACCEPT = 'application/json';
    /**#@- */

    /**#@+
     * oAuth constants
     */
    const OAUTH_SIGNATURE_METHOD = 'HMAC-SHA1';
    const OAUTH_VERSION = 1;
    /**#@- */

    /**
     * URL path
     *
     * @var string
     */
    protected $_urlPath = '/api/rest';

    /** @var Zend_Http_Client */
    protected $_client = null;

    /**
     * @var Mage_OAuth_Model_Token
     */
    protected $_token = null;

    /**
     * @var Mage_OAuth_Model_Consumer
     */
    protected $_consumer = null;

    /**
     * Init webservice
     *
     * @param array $options
     * @return void
     */
    public function init($options = null)
    {
        $this->_client = new Zend_Http_Client(TESTS_WEBSERVICE_URL, array(
            'adapter' => 'Zend_Http_Client_Adapter_Curl'
        ));
        $this->_client->setHeaders(array(
            'Version' => self::API_VERSION,
            'Content-Type' => self::CONTENT_TYPE,
            'Accept' => self::ACCEPT,
        ));

        // Create oAuth token for REST adapter (admin/customer)
        if (isset($options['type']) && $options['type'] != 'guest') {
            $this->_createToken($options['type']);
        }
    }

    /**
     * Create oAuth token for specified user type
     *
     * @param string $userType
     * @return void
     * @throws Exception
     */
    protected function _createToken($userType)
    {
        $this->_consumer = Mage::getModel('oauth/consumer')
            ->load(TESTS_OAUTH_CONSUMER, 'key');
        $this->_token = Mage::getModel('oauth/token');
        $this->_token->createRequestToken($this->_consumer->getId(), TESTS_WEBSERVICE_URL);

        if ($userType == 'customer') {
            $website = Mage::app()->getWebsite();
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId($website->getId())
                ->loadByEmail(TESTS_CUSTOMER_EMAIL);
            $userId = $customer->getId();
            if (!$userId) {
                throw new Exception('Test Customer not found.');
            }
        } elseif ($userType == 'admin') {
            /** @var $admin Mage_Admin_Model_User */
            $admin = Mage::getModel('admin/user')->loadByUsername(TESTS_ADMIN_USERNAME);
            $userId = $admin->getId();
            if (!$userId) {
                throw new Exception('Test Admin not found.');
            }
        }

        $this->_token->authorize($userId, $userType)->convertToAccess();
    }

    /**
     * REST GET
     *
     * @param string $resourceName
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callGet($resourceName)
    {
        $resourceUri = $this->_getResourceUri($resourceName);

        $this->_prepareRequest($resourceUri, Zend_Http_Client::GET);

        $zendHttpResponse = $this->_client->request(Zend_Http_Client::GET);
        $responseDecorator = new Magento_Test_Webservice_Rest_ResponseDecorator($zendHttpResponse);
        return $responseDecorator;
    }

    /**
     * REST POST
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callPost($resourceName, $params)
    {
        $resourceUri = $this->_getResourceUri($resourceName);
        $this->_prepareRequest($resourceUri, Zend_Http_Client::POST);
        $this->_prepareRequestBody($params);

        $zendHttpResponse = $this->_client->request(Zend_Http_Client::POST);
        $responseDecorator = new Magento_Test_Webservice_Rest_ResponseDecorator($zendHttpResponse);
        return $responseDecorator;
    }

    /**
     * REST PUT
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callPut($resourceName, $params)
    {
        $resourceUri = $this->_getResourceUri($resourceName);
        $this->_prepareRequest($resourceUri, Zend_Http_Client::PUT);
        $this->_prepareRequestBody($params);

        $zendHttpResponse = $this->_client->request(Zend_Http_Client::PUT);
        $responseDecorator = new Magento_Test_Webservice_Rest_ResponseDecorator($zendHttpResponse);
        return $responseDecorator;
    }

    /**
     * REST DELETE
     *
     * @param string $resourceName
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callDelete($resourceName)
    {
        $resourceUri = $this->_getResourceUri($resourceName);
        $this->_prepareRequest($resourceUri, Zend_Http_Client::DELETE);

        $zendHttpResponse = $this->_client->request(Zend_Http_Client::DELETE);
        $responseDecorator = new Magento_Test_Webservice_Rest_ResponseDecorator($zendHttpResponse);
        return $responseDecorator;
    }

    /**
     * Prepare request body, encode input params array
     *
     * @param array $params
     * @return void
     */
    protected function _prepareRequestBody($params)
    {
        $contentType = $this->_client->getHeader('Content-Type');
        $interpreter = Magento_Test_Webservice_Rest_Interpreter_Factory::getInterpreter($contentType);
        $this->_client->setRawData($interpreter->encode($params));
    }

    /**
     * Prepare request and set oAuth headers if required
     *
     * @param string $resourceUri
     * @param string $requestMethod
     * @return void
     */
    protected function _prepareRequest($resourceUri, $requestMethod)
    {
        $this->_client->setUri($resourceUri);

        if ($this->_token !== null) {
            $utility = new Zend_Oauth_Http_Utility();
            $params = array(
                'oauth_consumer_key'     => $this->_consumer->getKey(),
                'oauth_nonce'            => $utility->generateNonce(),
                'oauth_signature_method' => self::OAUTH_SIGNATURE_METHOD,
                'oauth_timestamp'        => $utility->generateTimestamp(),
                'oauth_version'          => self::OAUTH_VERSION,
                'oauth_token'            => $this->_token->getToken(),
            );
            $params['oauth_signature'] = $utility->sign($params,
                self::OAUTH_SIGNATURE_METHOD,
                $this->_consumer->getSecret(),
                $this->_token->getSecret(),
                $requestMethod,
                $resourceUri
            );

            $authHeaders = array('OAuth realm="Test Realm"');
            foreach ($params as $key => $value) {
                $authHeaders[] = $key . '="' . $value . '"';
            }
            $this->_client->setHeaders('Authorization', implode(',', $authHeaders));
        }
    }

    /**
     * Return client
     *
     * @return Zend_Http_Client
     */
    public function getClient() {
        return $this->_client;
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
     * Get client URL
     *
     * @param string $resourceName
     * @return string
     */
    protected function _getResourceUri($resourceName)
    {
        return TESTS_WEBSERVICE_URL. $this->_urlPath . '/' . $resourceName;
    }
}
