<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * SOAP API Request
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Controller_Request_Soap extends Mage_Webapi_Controller_RequestAbstract
{
    /**
     * Initialize API type.
     *
     * @param string|null $uri
     */
    public function __construct($uri = null)
    {
        $this->setApiType(Mage_Webapi_Controller_Front_Base::API_TYPE_SOAP);
        parent::__construct($uri);
    }

    /**
     * Identify versions of resources that should be used for API configuration file generation.
     *
     * @return array
     * @throws Mage_Webapi_Exception When GET parameters are invalid
     */
    public function getRequestedResources()
    {
        $helper = Mage::helper('Mage_Webapi_Helper_Data');
        $baseUrl = Mage::getBaseUrl();
        $wsdlParam = Mage_Webapi_Controller_Front_Soap::REQUEST_PARAM_WSDL;
        $resourcesParam = Mage_Webapi_Controller_Front_Soap::REQUEST_PARAM_RESOURCES;
        $exampleUrl = "{$baseUrl}api/soap?{$wsdlParam}&{$resourcesParam}[customer]=v1&{$resourcesParam}[catalog]=v1";
        $requestParams = array_keys($this->getParams());
        $allowedParams = array('api_type', $wsdlParam, $resourcesParam);
        $notAllowedParameters = array_diff($requestParams, $allowedParams);
        if (count($notAllowedParameters)) {
            $message = $helper->__('Not allowed parameters: %s', implode(', ', $notAllowedParameters)) . PHP_EOL
                . $helper->__('Please, use only "%s" and "%s". Example: ', $wsdlParam, $resourcesParam) . $exampleUrl;
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $requestedResources = $this->getParam($resourcesParam);
        if (empty($requestedResources) || !is_array($requestedResources) || empty($requestedResources)) {
            $message = $helper->__('Missing requested resources. Example: ') . $exampleUrl . PHP_EOL
                // TODO: change documentation link
                . $helper->__('See documentation: https://wiki.corp.x.com/display/APIA/New+API+module+architecture#NewAPImodulearchitecture-Resourcesversioning');
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        return $requestedResources;
    }
}
