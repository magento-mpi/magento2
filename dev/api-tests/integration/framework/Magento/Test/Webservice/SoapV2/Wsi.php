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

class Magento_Test_Webservice_SoapV2_Wsi extends Magento_Test_Webservice_SoapV2
{
    /**
     * Prepare parameters to be used in WS-I call
     *
     * @param mixed $params
     * @return stdClass
     */
    protected function _prepareParams($params)
    {
        if (is_object($params)) {
            return $params;
        }
        if (!is_array($params)) {
            $params = array($params);
        }
        $params['sessionId'] = $this->_session;

        return (object) $params;
    }

    /**
     * Replace "complexObjectArray" keys from array
     *
     * @param array $arg
     * @return array
     */
    protected function _replaceComplexObjectArray(array $arg)
    {
        $data = array();

        foreach ($arg as $key => $value) {
            if (is_array($value)) {
                $value = $this->_replaceComplexObjectArray($value);
            }
            if ('complexObjectArray' == $key) {
                $key = count($data);
            }
            $data[$key] = $value;
        }
        return 1 === count($data) ? reset($data) : $data;
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
        if ('login' !== $path) {
            $params = $this->_prepareParams($params);
        }
        try {
            $soapRes = call_user_func(array($this->_client, $soap2method), $params);
        } catch (SoapFault $e) {
            if ($this->_isShowInvalidResponse() && in_array($e->getMessage(), $this->_badRequestMessages)) {
                $e = new Magento_Test_Webservice_Exception(
                    sprintf('SoapClient should be get XML document but got following: "%s"', $this->getLastResponse())
                );
            }
            throw $e;
        }
        return (is_array($soapRes) || is_object($soapRes)) ? self::soapWsiResultToArray($soapRes) : $soapRes;
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
        return $this->call('login', (object) array('username' => $api, 'apiKey' => $key));
    }

    /**
     * Convert object to array recursively
     *
     * @param object $soapResult
     * @return array
     */
    public function soapWsiResultToArray($soapResult)
    {
        return $this->_replaceComplexObjectArray(
            Magento_Test_Webservice_SoapV2::soapResultToArray($soapResult)
        );
    }
}
