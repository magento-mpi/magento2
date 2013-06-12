<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Common SOAP operation model
 *
 * @deprecated Use NVP implementation to work with permissions
 *
 * @category   Saas
 * @package    Saas_Paypal
 * @author     Magento Saas Team <core@magentocommerce.com>
 */
abstract class Saas_Paypal_Model_Api_Soap_Operation_Abstract
{
    /**
     * Operation name for SOAP request.
     * Should be set in subclasses.
     *
     * @var string
     */
    protected $_methodName = '';

    /**
     * Request array map for the operation between SOAP request and data in Api model.
     * Should be set in subclasses.
     *
     * @var array
     */
    protected $_requestMap = array();

    /**
     * Response array map for the operation between SOAP response and data in Api model.
     * Should be set in subclasses.
     *
     * @var array
     */
    protected $_responseMap = array();

    /**
     * Global array of Api model's data keys to replace by *** for debug logs.
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array();

    /**
     * Operation's specific array of Api model's data keys to replace by *** for debug logs.
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeysDefault = array();

    /**
     * Returns SOAP method name
     *
     * @return string
     */
    public function getMethodName()
    {
        return $this->_methodName;
    }

    /**
     * Returns formatted request for SOAP object from Api model.
     *
     * @param Saas_Paypal_Model_Api_Soap $api
     * @return stdClass
     */
    public function getRequest(Saas_Paypal_Model_Api_Soap $api)
    {
        return $this->_getRequestFromMap($api, $this->_requestMap);
    }

    /**
     * Returns formatted request for SOAP object from Api model and method's map.
     *
     * @param Saas_Paypal_Model_Api_Soap $api
     * @param array $map
     * @return stdClass
     */
    protected function _getRequestFromMap(Saas_Paypal_Model_Api_Soap $api, array $map)
    {
        $request = new stdClass();
        foreach ($map as $requestField => $apiField) {
            if (is_array($apiField)) {
                $request->$requestField = $this->_getRequestFromMap($api, $apiField);
            } else {
                $request->$requestField = $api->getDataUsingMethod($apiField);
            }
        }
        return $request;
    }

    /**
     * Returns array from stdClass object.
     * Needed for Magento debug logger model.
     *
     * @param stdClass $object
     * @return array
     */
    public function toArray($object)
    {
        $result = array();
        foreach ($object as $property => $value) {
            if (is_object($value)) {
                $result[$property] = $this->toArray($value);
            } else {
                $result[$property] = $value;
            }
        }
        return $result;
    }

    /**
     * Returns total operation's debug private keys.
     *
     * @return array
     */
    public function getDebugPrivateDataKeys()
    {
        return array_merge($this->_debugReplacePrivateDataKeysDefault, $this->_debugReplacePrivateDataKeys);
    }

    /**
     * Set data into Api model from SOAP response
     *
     * @param Saas_Paypal_Model_Api_Soap $api
     * @param stdClass $response
     */
    public function setResponse(Saas_Paypal_Model_Api_Soap $api, $response)
    {
        $this->_setResponseFromMap($api, $response, $this->_responseMap);
    }

    /**
     * Set data into Api model from SOAP response by map
     *
     * @param Saas_Paypal_Model_Api_Soap $api
     * @param stdClass $response
     * @param array $map
     */
    protected function _setResponseFromMap(Saas_Paypal_Model_Api_Soap $api, $response, array $map)
    {
        foreach ($map as $responseField => $apiField) {
            if (!isset($response->$responseField)) {
                continue;
            }
            if (is_array($apiField)) {
                $this->_setResponseFromMap($api, $response->$responseField, $apiField);
            } else {
                $api->setDataUsingMethod($apiField, $response->$responseField);
            }
        }
    }
}
