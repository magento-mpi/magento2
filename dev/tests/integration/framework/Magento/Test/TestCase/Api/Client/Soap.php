<?php
/**
 * Test client for SOAP API testing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_TestCase_Api_Client_Soap
{
    /**
     * Class of exception web services client throws
     *
     * @const
     */
    const EXCEPTION_CLASS = 'SoapFault';

    /**
     * Session ID
     *
     * @var string
     */
    protected $_session;

    /**
     * Webservice full URL
     *
     * @var string
     */
    protected $_url;

    /**
     * URL path
     *
     * @var string
     */
    protected $_urlPath = '/api/soap?wsdl=1';

    /**
     * Function prefixes
     *
     * @var array
     */
    protected $_configFunction;

    /**
     * Resources alias
     *
     * @var array
     */
    protected $_configAlias;

    /**
     * SOAP client adapter
     *
     * @var Zend\Soap\Client
     */
    protected $_client;

    /**
     * Bad request messages
     *
     * @var array
     */
    protected $_badRequestMessages = array(
        'Failed to parse response',
        'Invalid response',
        'looks like we got no XML document',
        'Wrong Version'
    );

    /**
     * Initialize
     *
     * @param array|null $options
     * @return Magento_Test_TestCase_Api_Client_Soap
     * @throws SoapFault
     */
    public function init($options = null)
    {
        // force to not use WSDL cache it helps to avoid clean WSDL cache every time WS-I - not WS-I mode changes
        $options['cache_wsdl'] = WSDL_CACHE_NONE;

        $this->_client = new Zend\Soap\Client($this->getClientUrl($options));
        $this->_client->setSoapVersion(SOAP_1_1);

        $this->_configFunction = Mage::getSingleton('Mage_Api_Model_Config')
            ->getNode('v2/resources_function_prefix')
            ->children();
        $this->_configAlias = Mage::getSingleton('Mage_Api_Model_Config')->getNode('resources_alias')->children();

        try {
            $apiUser = isset($options['api_user']) ? $options['api_user'] : TESTS_WEBSERVICE_USER;
            $apiKey = isset($options['api_key']) ? $options['api_key'] : TESTS_WEBSERVICE_APIKEY;
            $sessionId = $this->login($apiUser, $apiKey);
        } catch (SoapFault $e) {
            $this->_throwExceptionBadRequest($e);
            throw $e;
        }

        $this->setSession($sessionId);
        return $this;
    }

    /**
     * Try to throw exception with show response
     *
     * @param SoapFault $e
     * @return Magento_Test_TestCase_Api_Client_Soap
     * @throws RuntimeException
     */
    protected function _throwExceptionBadRequest(SoapFault $e)
    {
        if ($this->_isShowInvalidResponse()
            && in_array($e->getMessage(), $this->_badRequestMessages)
        ) {
            throw new RuntimeException(sprintf(
                'SOAP client should be get XML document but got following: "%s"',
                $this->getLastResponse()
            ));
        }
        return $this;
    }

    /**
     * Convert object to array recursively
     *
     * @param object $soapResult
     * @return array
     */
    public static function soapResultToArray($soapResult)
    {
        if (is_object($soapResult) && null !== ($_data = get_object_vars($soapResult))) {
            foreach ($_data as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $_data[$key] = self::soapResultToArray($value);
                }
            }
            return $_data;
        } elseif (is_array($soapResult)) {
            $_data = array();
            foreach ($soapResult as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $_data[$key] = self::soapResultToArray($value);
                }
            }
            return $_data;
        }
        return array();
    }

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
     * Do soap call
     *
     * @param $path
     * @param array $params
     * @return array|mixed
     * @throws RuntimeException
     * @throws SoapFault
     */
    public function call($path, $params = array())
    {
        if (strpos($path, '.')) {
            $pathExploded = explode('.', $path);

            $pathApi = $pathExploded[0];
            $pathMethod = isset($pathExploded[1]) ? $pathExploded[1] : '';
            $pathMethod[0] = strtoupper($pathMethod[0]);
            foreach ($this->_configAlias as $key => $value) {
                if ((string)$value == $pathApi) {
                    $pathApi = $key;
                    break;
                }
            }

            $methodName = (string)$this->_configFunction->$pathApi;
            $methodName .= $pathMethod;
        } else {
            $methodName = $path;
        }

        if (!is_array($params)) {
            $params = array($params);
        }
        //add session ID as first param but except for "login" method
        if ('login' != $methodName) {
            array_unshift($params, $this->_session);
        }

        try {
            $soapResult = call_user_func_array(array($this->_client, $methodName), $params);
        } catch (SoapFault $e) {
            if ($this->_isShowInvalidResponse()
                && in_array($e->getMessage(), $this->_badRequestMessages)
            ) {
                throw new RuntimeException(sprintf(
                    'Well formed XML is expected, "%s" given instead.',
                    $this->getLastResponse()
                ));
            }
            throw $e;
        }

        if (is_array($soapResult) || is_object($soapResult)) {
            $result = self::soapResultToArray($soapResult);
        } else {
            $result = $soapResult;
        }

        return $result;
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
     * @return Magento_Test_TestCase_Api_Client_Soap
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
     * @return null|Zend\Soap\Client
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
     * Get client URL
     *
     * @param array|null $options
     * @return string
     */
    public function getClientUrl($options = null)
    {
        if (null === $this->_url) {
            $webserviceUrl = isset($options['webservice_url']) ? $options['webservice_url'] : TESTS_WEBSERVICE_URL;
            $this->_url = rtrim($webserviceUrl, '/') . '/' . ltrim($this->_urlPath, '/');
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
