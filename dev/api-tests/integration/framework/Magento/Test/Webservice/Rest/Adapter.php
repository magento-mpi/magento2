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
     * @var Mage_Oauth_Model_Token
     */
    protected $_token = null;

    /**
     * @var Mage_Oauth_Model_Consumer
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
        $this->_client = new Zend_Http_Client(TESTS_WEBSERVICE_URL);
        $this->_client->setHeaders(array(
            'Version' => self::API_VERSION,
            'Content-Type' => self::CONTENT_TYPE,
            'Accept' => self::ACCEPT,
        ));

        // Create oAuth token for REST adapter (admin/customer)
        if (isset($options['type']) && $options['type'] != 'guest') {
            $this->_loadToken($options['type']);
        }
    }

    /**
     * Create authorized access token
     *
     * @param string $consumerId
     * @param string $userType
     * @param int $userId
     */
    protected function _createToken($consumerId, $userType, $userId)
    {
        $this->_token = Mage::getModel('Mage_Oauth_Model_Token');

        $this->_token->createRequestToken($consumerId, TESTS_WEBSERVICE_URL)
            ->authorize($userId, $userType)
            ->convertToAccess();
    }

    /**
     * Create oAuth token for specified user type
     *
     * @param string $userType
     * @return void
     * @throws Exception
     */
    protected function _loadToken($userType)
    {
        $this->_consumer = Mage::getModel('Mage_Oauth_Model_Consumer')
            ->load(TESTS_OAUTH_CONSUMER, 'key');

        if ($userType == 'customer') {
            $website = Mage::app()->getWebsite();
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('Mage_Customer_Model_Customer')
                ->setWebsiteId($website->getId())
                ->loadByEmail(TESTS_CUSTOMER_EMAIL);
            $userId = $customer->getId();
            if (!$userId) {
                throw new Exception('Test Customer not found.');
            }
        } elseif ($userType == 'admin') {
            /** @var $admin Mage_User_Model_User */
            $admin = Mage::getModel('Mage_User_Model_User')->loadByUsername(TESTS_ADMIN_USERNAME);
            $userId = $admin->getId();
            if (!$userId) {
                throw new Exception('Test Admin not found.');
            }
        } else {
            throw new Exception("Invalid user type '{$userType}'.");
        }
        /** @var $tokenResource Mage_Oauth_Model_Resource_Token_Collection */
        $tokenResource = Mage::getResourceModel('Mage_Oauth_Model_Resource_Token_Collection');

        $tokenResource->addFilterByConsumerId($this->_consumer->getId())->addFilterByType('access');

        if ($userType == 'customer') {
            $tokenResource->addFilterByCustomerId($userId);
        } else {
            $tokenResource->addFilterByAdminId($userId);
        }
        if (($tokens = $tokenResource->getItems())) {
            $this->_token = reset($tokens);
        } else {
            $this->_createToken($this->_consumer->getId(), $userType, $userId);
        }
    }

    /**
     * REST GET
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callGet($resourceName, $params = array())
    {
        $resourceUri = $this->_getResourceUri($resourceName);
        $this->_prepareRequest($resourceUri, Zend_Http_Client::GET, $params);

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
        $this->_prepareRequest($resourceUri, Zend_Http_Client::POST, $params);

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
        $this->_prepareRequest($resourceUri, Zend_Http_Client::PUT, $params);

        $zendHttpResponse = $this->_client->request(Zend_Http_Client::PUT);
        $responseDecorator = new Magento_Test_Webservice_Rest_ResponseDecorator($zendHttpResponse);
        return $responseDecorator;
    }

    /**
     * REST DELETE
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callDelete($resourceName, $params = array())
    {
        $resourceUri = $this->_getResourceUri($resourceName);
        $this->_prepareRequest($resourceUri, Zend_Http_Client::DELETE, $params);

        $zendHttpResponse = $this->_client->request(Zend_Http_Client::DELETE);
        $responseDecorator = new Magento_Test_Webservice_Rest_ResponseDecorator($zendHttpResponse);
        return $responseDecorator;
    }

    /**
     * Prepare request and set oAuth headers if required
     *
     * @param string $resourceUri
     * @param string $requestMethod
     * @param array $requestParams
     * @return void
     */
    protected function _prepareRequest($resourceUri, $requestMethod, $requestParams = array())
    {
        if ($requestMethod == Zend_Http_Client::GET) {
            $resourceUri .= '?' . http_build_query($requestParams);
            $params = $requestParams;
        } else {
            $contentType = $this->_client->getHeader('Content-Type');
            $interpreter = Magento_Test_Webservice_Rest_Interpreter_Factory::getInterpreter($contentType);
            $this->_client->setRawData($interpreter->encode($requestParams));
            $params = array();
        }
        $this->_client->setUri($resourceUri);

        if ($this->_token !== null) {
            $utility = new Zend_Oauth_Http_Utility();
            $oauthParams =
            $oauthParams = array(
                'oauth_consumer_key'     => $this->_consumer->getKey(),
                'oauth_nonce'            => $utility->generateNonce(),
                'oauth_signature_method' => self::OAUTH_SIGNATURE_METHOD,
                'oauth_timestamp'        => $utility->generateTimestamp(),
                'oauth_version'          => self::OAUTH_VERSION,
//                'oauth_token'            => $this->_token->getToken(),
            );
            $oauthParams['oauth_signature'] = $utility->sign($params + $oauthParams,
                self::OAUTH_SIGNATURE_METHOD,
                $this->_consumer->getSecret(),
//                $this->_token->getSecret(),
                $requestMethod,
                $resourceUri
            );

            $authHeaders = array('OAuth realm="Test Realm"');
            foreach ($oauthParams as $key => $value) {
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
