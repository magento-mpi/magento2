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

class Magento_Test_Webservice_SoapV1 extends Magento_Test_Webservice_Abstract
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
    protected $_urlPath = '/api/soap?wsdl=1';

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
     * Init
     *
     * @param null|array $options
     * @return Magento_Test_Webservice_Abstract
     */
    public function init($options = null)
    {
        $this->_client = new Zend_Soap_Client($this->getClientUrl($options));
        $apiUser = isset($options['api_user']) ? $options['api_user'] : TESTS_WEBSERVICE_USER;
        $apiKey  = isset($options['api_key']) ? $options['api_key'] : TESTS_WEBSERVICE_APIKEY;
        $this->setSession($this->login($apiKey, $apiUser));
        return $this;
    }

    /**
     * Call API method
     *
     * @param string $path
     * @param array $params
     * @return array|string
     * @throws SoapFault
     */
    public function call($path, $params = array())
    {
        try {
            return $this->_client->call($this->_session, $path, $params);
        } catch (SoapFault $e) {
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
     * @throws SoapFault
     */
    public function login($username, $apiKey)
    {
        try {
            return $this->_client->login($username, $apiKey);
        } catch (SoapFault $e) {
            $this->_throwExceptionBadRequest($e);
            throw $e;
        }
    }

    /**
     * Try to throw exception with show response
     *
     * @param SoapFault $e
     * @throws Magento_Test_Webservice_Exception
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
