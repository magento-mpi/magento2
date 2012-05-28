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

class Magento_Test_Webservice_SoapV2 extends Magento_Test_Webservice_Abstract
{
    /**
     * Class of exception web services client throws
     *
     * @const
     */
    const EXCEPTION_CLASS = 'SoapFault';

    /**
     * URL path
     *
     * @var string
     */
    protected $_urlPath = '/api/v2_soap?wsdl=1';

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
     * @var Zend_Soap_Client
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
     * @return Magento_Test_Webservice_SoapV2
     */
    public function init($options = null)
    {
        // force to not use WSDL cache it helps to avoid clean WSDL cache every time WS-I - not WS-I mode changes
        $options['cache_wsdl'] = WSDL_CACHE_NONE;

        $this->_client = new Zend_Soap_Client($this->getClientUrl(), $options);
        $this->_client->setSoapVersion(SOAP_1_1);

        $this->_configFunction = Mage::getSingleton('Mage_Api_Model_Config')->getNode('v2/resources_function_prefix')->children();
        $this->_configAlias    = Mage::getSingleton('Mage_Api_Model_Config')->getNode('resources_alias')->children();

        try {
            $sessionId = $this->login(TESTS_WEBSERVICE_USER, TESTS_WEBSERVICE_APIKEY);
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
     * @return Magento_Test_Webservice_SoapV2
     * @throws SoapFault
     */
    protected function _throwExceptionBadRequest(SoapFault $e)
    {
        if ($this->_isShowInvalidResponse()
            && in_array($e->getMessage(), $this->_badRequestMessages)
        ) {
            throw new Magento_Test_Webservice_Exception(sprintf(
                'SOAP client should be get XML document but got following: "%s"',
                $this->getLastResponse()));
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
        } elseif (is_array($soapResult)){
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
     * Do soap call
     *
     * @param string $path
     * @param array $params
     * @return array|mixed
     */
    public function call($path, $params = array())
    {
        if (strpos($path, '.')) {
            $pathExploded  = explode('.', $path);

            $pathApi       = $pathExploded[0];
            $pathMethod    = isset($pathExploded[1]) ? $pathExploded[1] : '';
            $pathMethod[0] = strtoupper($pathMethod[0]);
            foreach ($this->_configAlias as $key => $value) {
                if ((string) $value == $pathApi) {
                    $pathApi = $key;
                    break;
                }
            }

            $soap2method = (string) $this->_configFunction->$pathApi;
            $soap2method .= $pathMethod;
        } else {
            $soap2method = $path;
        }

        if (!is_array($params)) {
            $params = array($params);
        }
        //add session ID as first param but except for "login" method
        if ('login' != $soap2method) {
            array_unshift($params, $this->_session);
        }

        try {
            $soapResult = call_user_func_array(array($this->_client, $soap2method), $params);
        } catch (SoapFault $e) {
            if ($this->_isShowInvalidResponse()
                && in_array($e->getMessage(), $this->_badRequestMessages)
            ) {
                throw new Magento_Test_Webservice_Exception(sprintf(
                    'SoapClient should be get XML document but got following: "%s"',
                    $this->getLastResponse()));
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
}
