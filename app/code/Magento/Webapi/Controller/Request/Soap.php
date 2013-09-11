<?php
/**
 * Soap API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Request;

class Soap extends \Magento\Webapi\Controller\Request
{
    /**
     * Initialize dependencies.
     *
     * @param string|null $uri
     */
    public function __construct($uri = null)
    {
        parent::__construct(\Magento\Webapi\Controller\Front::API_TYPE_SOAP, $uri);
    }

    /**
     * Identify versions of resources that should be used for API configuration generation.
     *
     * @return array
     * @throws \Magento\Webapi\Exception When GET parameters are invalid
     */
    public function getRequestedResources()
    {
        $wsdlParam = \Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_WSDL;
        $resourcesParam = \Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_RESOURCES;
        $requestParams = array_keys($this->getParams());
        $allowedParams = array(\Magento\Webapi\Controller\Request::PARAM_API_TYPE, $wsdlParam, $resourcesParam);
        $notAllowedParameters = array_diff($requestParams, $allowedParams);
        if (count($notAllowedParameters)) {
            $message = __('Not allowed parameters: %1. ', implode(', ', $notAllowedParameters))
                . __('Please use only "%1" and "%2".', $wsdlParam, $resourcesParam);
            throw new \Magento\Webapi\Exception($message, \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
        }

        $requestedResources = $this->getParam($resourcesParam);
        if (empty($requestedResources) || !is_array($requestedResources)) {
            $message = __('Requested resources are missing.');
            throw new \Magento\Webapi\Exception($message, \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
        }
        return $requestedResources;
    }
}
