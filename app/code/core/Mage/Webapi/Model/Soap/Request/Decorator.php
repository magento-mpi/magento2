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
 * SOAP API Request decorator
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 * @method mixed getParam()
 * @method array getParams()
 */
class Mage_Webapi_Model_Soap_Request_Decorator extends Mage_Webapi_Model_Request_DecoratorAbstract
{
    /**
     * Identify versions of modules that should be used for API configuration file generation.
     *
     * @return array
     * @throws RuntimeException when get paramters are invalid
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
            throw new RuntimeException($message);
        }

        $requestedModules = $this->getParam('modules');
        if (empty($requestedModules) || !is_array($requestedModules) || empty($requestedModules)) {
            $message = $helper->__('Missing requested modules. Example: ') . $exampleUrl . PHP_EOL
                // TODO: change documentation link
                . $helper->__('See documentation: https://wiki.corp.x.com/display/APIA/New+API+module+architecture#NewAPImodulearchitecture-Resourcesversioning');
            throw new RuntimeException($message);
        }
        return $requestedModules;
    }
}
