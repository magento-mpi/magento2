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
    private $_configFunction;
    private $_configAlias;

    public function init()
    {
        $this->_client = new Zend_Soap_Client(TESTS_WEBSERVICE_URL . '/api/v2_soap/?wsdl=1');
        $this->_client->setSoapVersion(SOAP_1_1);
        $this->_session        = $this->_client->login(TESTS_WEBSERVICE_USER, TESTS_WEBSERVICE_APIKEY);
        $this->_configFunction = Mage::getSingleton('api/config')->getNode('v2/resources_function_prefix')->children();
        $this->_configAlias    = Mage::getSingleton('api/config')->getNode('resources_alias')->children();
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
        array_unshift($params, $this->_session);

        $soapResult = call_user_func_array(array($this->_client, $soap2method), $params);

        if (is_array($soapResult) || is_object($soapResult)) {
            $result = self::soapResultToArray($soapResult);
        } else {
            $result = $soapResult;
        }

        return $result;
    }
}
