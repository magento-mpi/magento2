<?php
/**
 * Soap API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Soap_Request extends Mage_Webapi_Controller_Request
{
    /** @var Mage_Core_Model_App */
    protected $_application;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_App $application
     * @param Mage_Webapi_Helper_Data $helper
     * @param string|null $uri
     */
    public function __construct(
        Mage_Core_Model_App $application,
        Mage_Webapi_Helper_Data $helper,
        $uri = null
    ) {
        parent::__construct($application, $uri);
        $this->_helper = $helper;
    }

    /**
     * Identify versions of resources that should be used for API configuration generation.
     * TODO : This is getting called twice within a single request. Need to cache.
     *
     * @return array
     * @throws Mage_Webapi_Exception When GET parameters are invalid
     */
    public function getRequestedServices()
    {
        $wsdlParam = Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL;
        $servicesParam = Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_SERVICES;
        $requestParams = array_keys($this->getParams());
        $allowedParams = array($wsdlParam, $servicesParam);
        $notAllowedParameters = array_diff($requestParams, $allowedParams);
        if (count($notAllowedParameters)) {
            $message = $this->_helper->__('Not allowed parameters: %s. ', implode(', ', $notAllowedParameters))
                . $this->_helper->__('Please use only "%s" and "%s".', $wsdlParam, $servicesParam);
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
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
     * @throws Mage_Webapi_Exception
     */
    protected function _convertRequestParamToServiceArray($param)
    {
        $serviceSeparator = ',';
        //TODO: This should be a globally used pattern in Webapi module
        $serviceVerPattern = "[a-zA-Z\d]*V[\d]+";
        $regexp = "/^($serviceVerPattern)([$serviceSeparator]$serviceVerPattern)*$/";
        //Check if the $param is of valid format
        if (empty($param) || !preg_match($regexp, $param)) {
            $message = $this->_helper->__('Incorrect format of WSDL request URI or Requested services are missing.');
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
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
