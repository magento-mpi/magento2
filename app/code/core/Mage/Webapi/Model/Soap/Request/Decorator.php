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
 */
class Mage_Webapi_Model_Soap_Request_Decorator extends Mage_Webapi_Model_Request_DecoratorAbstract
{
    /**
     * Identify versions of modules that should be used for API configuration file generation.
     *
     * @return array
     * @throws RuntimeException when header value is invalid
     */
    public function getRequestedModules()
    {
        $requestedModules = $this->getParam('modules');
        if (empty($requestedModules) || !is_array($requestedModules) || empty($requestedModules)) {
            $message = "Missing requested modules.\n"
                . "Example: http://magentohost/api/soap?wsdl&modules[Mage_Customer]=v1";
            throw new RuntimeException($message);
        }
        return $requestedModules;
    }
}
