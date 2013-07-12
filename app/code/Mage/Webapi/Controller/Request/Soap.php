<?php
/**
 * Soap API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request_Soap extends Mage_Webapi_Controller_Request
{
    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_Config $config
     * @param Mage_Webapi_Helper_Data $helper
     * @param string|null $uri
     */
    public function __construct(Mage_Core_Model_Config $config, Mage_Webapi_Helper_Data $helper, $uri = null)
    {
        parent::__construct($config, Mage_Webapi_Controller_Front::API_TYPE_SOAP, $uri);
        $this->_helper = $helper;
    }

    /**
     * Identify versions of resources that should be used for API configuration generation.
     * FIXME : This is getting called twice within a single request. Need to cache.
     *
     * @return array
     * @throws Mage_Webapi_Exception When GET parameters are invalid
     */
    public function getRequestedResources()
    {
        $wsdlParam = Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL;
        $resourcesParam = Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_RESOURCES;
        $requestParams = array_keys($this->getParams());
        $allowedParams = array(Mage_Webapi_Controller_Request::PARAM_API_TYPE, $wsdlParam, $resourcesParam);
        $notAllowedParameters = array_diff($requestParams, $allowedParams);
        if (count($notAllowedParameters)) {
            $message = $this->_helper->__('Not allowed parameters: %s. ', implode(', ', $notAllowedParameters))
                . $this->_helper->__('Please use only "%s" and "%s".', $wsdlParam, $resourcesParam);
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $param = $this->getParam($resourcesParam);
        return $this->convertReqParamToServiceArray($param);
    }

    /**
     * Function to extract the resources query param value and return associative array of 'resource' => 'version'
     * eg Given testModule1AllSoapAndRest:V1,testModule2AllSoapNoRest:V1
     * validate, process and return below :
     * array (
     *      'testModule1AllSoapAndRest' => 'V1',
     *       'testModule2AllSoapNoRest' => 'V1',
     *      )
     *
     * @param $param
     * @return array
     * @throws Mage_Webapi_Exception
     */
    protected function convertReqParamToServiceArray($param)
    {
        $serviceSeparator = ",";
        $serviceVerSeparator = ":";
        //TODO: This should be a globally used pattern in Webapi module
        $serviceVerPattern = "[a-zA-Z\d]*[$serviceVerSeparator][V][\d]+";
        $regexp = "/^($serviceVerPattern)([$serviceSeparator]$serviceVerPattern)*$/";
        if (empty($param) || !preg_match($regexp, $param)) {
            $message = $this->_helper->__('Incorrect format of WSDL request URI or Requested resources are missing');
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        $serviceVerArr = explode($serviceSeparator, $param);
        $serviceArr = array();
        foreach ($serviceVerArr as $service) {
            $arr = explode($serviceVerSeparator, $service);
            if (array_key_exists($arr[0], $serviceArr)) {
                $message = $this->_helper->__("Resource '$arr[0]' cannot be requested more than once");
                throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
            } else {
                $serviceArr[$arr[0]] = $arr[1];
            }
        }
        return $serviceArr;
    }
}
