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
     * @return Magento_Test_Webservice_SoapV1
     */
    public function init($options = null)
    {
        $this->_client = new Zend_Soap_Client($this->getClientUrl(), $options);
        $this->setSession($this->login(TESTS_WEBSERVICE_USER, TESTS_WEBSERVICE_APIKEY));
        return $this;
    }

    /**
     *  Call API methods
     *
     * @param $path
     * @param array $params
     * @return string
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
     * @throws Zend_XmlRpc_Client_FaultException
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
     * @return Magento_Test_Webservice_SoapV1
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
