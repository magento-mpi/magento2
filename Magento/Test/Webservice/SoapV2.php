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
    private $_configFunction;

    /**
     * Resources alias
     *
     * @var array
     */
    private $_configAlias;

    /**
     * SOAP client adapter
     *
     * @var Zend_Soap_Client
     */
    protected $_client;

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

        $this->_configFunction = Mage::getSingleton('api/config')->getNode('v2/resources_function_prefix')->children();
        $this->_configAlias    = Mage::getSingleton('api/config')->getNode('resources_alias')->children();

        $this->setSession($this->login(TESTS_WEBSERVICE_USER, TESTS_WEBSERVICE_APIKEY));
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

        //add session ID as first param but except for "login" method
        if ('login' != $soap2method) {
            array_unshift($params, $this->_session);
        }

        try {
            $soapResult = call_user_func_array(array($this->_client, $soap2method), $params);
        } catch (SoapFault $e) {
            if ($this->_isShowInvalidResponse()
                && ('looks like we got no XML document' == $e->faultstring
                || $e->getMessage() == 'Wrong Version')
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
