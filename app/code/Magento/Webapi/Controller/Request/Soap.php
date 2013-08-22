<?php
/**
 * Soap API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Request_Soap extends Magento_Webapi_Controller_Request
{
    /**
     * Initialize dependencies.
     *
     * @param string|null $uri
     */
    public function __construct($uri = null)
    {
        parent::__construct(Magento_Webapi_Controller_Front::API_TYPE_SOAP, $uri);
    }

    /**
     * Identify versions of resources that should be used for API configuration generation.
     *
     * @return array
     * @throws Magento_Webapi_Exception When GET parameters are invalid
     */
    public function getRequestedResources()
    {
        $wsdlParam = Magento_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL;
        $resourcesParam = Magento_Webapi_Model_Soap_Server::REQUEST_PARAM_RESOURCES;
        $requestParams = array_keys($this->getParams());
        $allowedParams = array(Magento_Webapi_Controller_Request::PARAM_API_TYPE, $wsdlParam, $resourcesParam);
        $notAllowedParameters = array_diff($requestParams, $allowedParams);
        if (count($notAllowedParameters)) {
            $message = __('Not allowed parameters: %1. ', implode(', ', $notAllowedParameters))
                . __('Please use only "%1" and "%2".', $wsdlParam, $resourcesParam);
            throw new Magento_Webapi_Exception($message, Magento_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $requestedResources = $this->getParam($resourcesParam);
        if (empty($requestedResources) || !is_array($requestedResources)) {
            $message = __('Requested resources are missing.');
            throw new Magento_Webapi_Exception($message, Magento_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        return $requestedResources;
    }
}
