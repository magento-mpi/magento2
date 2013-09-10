<?php
/**
 * Soap API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Soap_Request extends Magento_Webapi_Controller_Request
{
    /** @var Magento_Core_Model_App */
    protected $_application;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Core_Model_App $application
     * @param string|null $uri
     */
    public function __construct(Magento_Core_Model_App $application, $uri = null)
    {
        parent::__construct($application, $uri);
    }

    /**
     * Identify versions of resources that should be used for API configuration generation.
     * TODO : This is getting called twice within a single request. Need to cache.
     *
     * @return array
     * @throws Magento_Webapi_Exception When GET parameters are invalid
     */
    public function getRequestedServices()
    {
        $wsdlParam = Magento_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL;
        $servicesParam = Magento_Webapi_Model_Soap_Server::REQUEST_PARAM_SERVICES;
        $requestParams = array_keys($this->getParams());
        $allowedParams = array($wsdlParam, $servicesParam);
        $notAllowedParameters = array_diff($requestParams, $allowedParams);
        if (count($notAllowedParameters)) {
            $message = __('Not allowed parameters: %1. ', implode(', ', $notAllowedParameters))
                . __('Please use only %1 and %2.', $wsdlParam, $servicesParam);
            throw new Magento_Webapi_Exception($message, Magento_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $param = $this->getParam($servicesParam);
        return $this->_convertRequestParamToServiceArray($param);
    }

    /**
     * Extract the resources query param value and return associative array of the form 'resource' => 'version'
     *
     * @param string $param eg <pre> testModule1AllSoapAndRest:V1,testModule2AllSoapNoRest:V1 </pre>
     * @return array <pre> eg array (
     *      'testModule1AllSoapAndRest' => 'V1',
     *       'testModule2AllSoapNoRest' => 'V1',
     *      )</pre>
     * @throws Magento_Webapi_Exception
     */
    protected function _convertRequestParamToServiceArray($param)
    {
        $serviceSeparator = ',';
        //TODO: This should be a globally used pattern in Webapi module
        $serviceVerPattern = "[a-zA-Z\d]*V[\d]+";
        $regexp = "/^($serviceVerPattern)([$serviceSeparator]$serviceVerPattern)*$/";
        //Check if the $param is of valid format
        if (empty($param) || !preg_match($regexp, $param)) {
            $message = __('Incorrect format of WSDL request URI or Requested services are missing.');
            throw new Magento_Webapi_Exception($message, Magento_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        //Split the $param string to create an array of 'service' => 'version'
        $serviceVersionArray = explode($serviceSeparator, $param);
        $serviceArray = array();
        foreach ($serviceVersionArray as $service) {
            $serviceArray[] = $service;
        }
        return $serviceArray;
    }
}
