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
     * @param Mage_Webapi_Helper_Data $helper
     * @param string|null $uri
     */
    public function __construct(Mage_Webapi_Helper_Data $helper, $uri = null)
    {
        parent::__construct(Mage_Webapi_Controller_Front::API_TYPE_SOAP, $uri);
        $this->_helper = $helper;
    }

    /**
     * Identify versions of resources that should be used for API configuration generation.
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

        $requestedResources = $this->getParam($resourcesParam);
        if (empty($requestedResources) || !is_array($requestedResources)) {
            $message = $this->_helper->__('Requested resources are missing.');
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        return $requestedResources;
    }
}
