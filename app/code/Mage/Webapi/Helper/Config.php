<?php
use Zend\Server\Reflection\ReflectionMethod;

/**
 * Webapi config helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Helper_Config extends Mage_Core_Helper_Abstract
{
    /**
     * Normalize short type names to full type names.
     *
     * @param string $type
     * @return string
     */
    public function normalizeType($type)
    {
        $normalizationMap = array(
            'str' => 'string',
            'integer' => 'int',
            'bool' => 'boolean',
            'mixed' => 'anyType' // TODO: Temporary solution
        );

        return isset($normalizationMap[$type]) ? $normalizationMap[$type] : $type;
    }

    /**
     * Translate service interface name into service name.
     * Example:
     * <pre>
     * - Mage_Customer_Service_CustomerInterfaceV1         => customer          // $preserveVersion == false
     * - Mage_Customer_Service_Customer_AddressInterfaceV1 => customerAddressV1 // $preserveVersion == true
     * - Mage_Catalog_Service_ProductInterfaceV2           => catalogProductV2  // $preserveVersion == true
     * </pre>
     *
     * @param string $interfaceName
     * @param bool $preserveVersion Should version be preserved during interface name conversion into service name
     * @return string
     * @throws InvalidArgumentException
     */
    public function getServiceName($interfaceName, $preserveVersion = true)
    {
        $serviceNameParts = $this->getServiceNameParts($interfaceName, $preserveVersion);
        return lcfirst(implode('', $serviceNameParts));
    }

    /**
     * Identify the list of service name parts including subservices using class name.
     *
     * Examples of input/output pairs: <br/>
     * - 'Mage_Customer_Service_Customer_AddressInterfaceV1' => array('Customer', 'Address', 'V1') <br/>
     * - 'Vendor_Customer_Service_Customer_AddressInterfaceV1' => array('VendorCustomer', 'Address', 'V1) <br/>
     * - 'Mage_Catalog_Service_ProductInterfaceV2' => array('CatalogProduct', 'V2')
     *
     * @param string $className
     * @param bool $preserveVersion Should version be preserved during class name conversion into service name
     * @return array
     * @throws InvalidArgumentException When class is not valid API service.
     */
    public function getServiceNameParts($className, $preserveVersion = false)
    {
        if (preg_match(Mage_Webapi_Config::SERVICE_CLASS_PATTERN, $className, $matches)) {
            $moduleNamespace = $matches[1];
            $moduleName = $matches[2];
            $moduleNamespace = ($moduleNamespace == 'Mage') ? '' : $moduleNamespace;
            $serviceNameParts = explode('_', trim($matches[3], '_'));
            if ($moduleName == $serviceNameParts[0]) {
                /** Avoid duplication of words in service name */
                $moduleName = '';
            }
            $parentServiceName = $moduleNamespace . $moduleName . array_shift($serviceNameParts);
            array_unshift($serviceNameParts, $parentServiceName);
            if ($preserveVersion) {
                $serviceVersion = $matches[4];
                $serviceNameParts[] = $serviceVersion;
            }
            return $serviceNameParts;
        }
        throw new InvalidArgumentException(sprintf('The service class name "%s" is invalid.', $className));
    }

    /**
     * Generate SOAP operation name.
     *
     * @param string $interfaceName e.g. Mage_Catalog_Service_ProductInterfaceV1
     * @param string $methodName e.g. create
     * @return string e.g. catalogProductCreate
     */
    public function getSoapOperation($interfaceName, $methodName)
    {
        $serviceName = $this->getServiceName($interfaceName);
        $operationName = $serviceName . ucfirst($methodName);
        return $operationName;
    }
}
