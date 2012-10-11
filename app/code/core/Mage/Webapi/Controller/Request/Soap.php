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
     * Identify versions of modules that should be used for API configuration file generation.
     *
     * @return array
     * @throws RuntimeException When GET parameters are invalid
     */
    public function getRequestedModules()
    {
        $helper = Mage::helper('Mage_Webapi_Helper_Data');
        $baseUrl = Mage::getBaseUrl();
        $exampleUrl = "{$baseUrl}api/soap?wsdl&modules[Mage_Customer]=v1&modules[Mage_Catalog]=v1";
        $requestParams = array_keys($this->getParams());
        $allowedParams = array('api_type', 'wsdl', 'modules');
        $notAllowedParameters = array_diff($requestParams, $allowedParams);
        if (count($notAllowedParameters)) {
            $message = $helper->__('Not allowed parameters: %s', implode(', ', $notAllowedParameters)) . PHP_EOL
                . $helper->__('Please, use only "wsdl" and "modules". Example: ') . $exampleUrl;
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $requestedModules = $this->getParam('modules');
        if (empty($requestedModules) || !is_array($requestedModules) || empty($requestedModules)) {
            $message = $helper->__('Missing requested modules. Example: ') . $exampleUrl . PHP_EOL
                // TODO: change documentation link
                . $helper->__('See documentation: https://wiki.corp.x.com/display/APIA/New+API+module+architecture#NewAPImodulearchitecture-Resourcesversioning');
            throw new Mage_Webapi_Exception($message, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        return $requestedModules;
    }
}
