<?php
/**
 * SOAP specific API config.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Config_Soap extends Mage_Webapi_Model_ConfigAbstract
{
    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Model_Config_Reader_Soap $reader
     * @param Mage_Webapi_Helper_Config $helper
     * @param Mage_Core_Model_App $application
     */
    public function __construct(
        Mage_Webapi_Model_Config_Reader_Soap $reader,
        Mage_Webapi_Helper_Config $helper,
        Mage_Core_Model_App $application
    ) {
        parent::__construct($reader, $helper, $application);
    }

    /**
     * Retrieve specific service version interface data.
     *
     * Perform metadata merge from previous method versions.
     *
     * @param string $serviceName
     * @param string $serviceVersion Two formats are acceptable: 'v1' and '1'
     * @return array
     * @throws RuntimeException
     */
    public function getServiceDataMerged($serviceName, $serviceVersion)
    {
        /** Allow to take service version in two formats: with prefix and without it */
        $serviceVersion = is_numeric($serviceVersion)
            ? self::VERSION_NUMBER_PREFIX . $serviceVersion
            : ucfirst($serviceVersion);
        $this->_checkIfServiceVersionExists($serviceName, $serviceVersion);
        $serviceData = array();
        foreach ($this->_data['services'][$serviceName]['versions'] as $version => $data) {
            $serviceData = array_replace_recursive($serviceData, $data);
            if ($version == $serviceVersion) {
                break;
            }
        }
        return $serviceData;
    }

    /**
     * Identify service name by operation name.
     *
     * If $serviceVersion is set, the check for operation validity in specified service version will be performed.
     * If $serviceVersion is not set, the only check will be: if service exists.
     *
     * @param string $operationName
     * @param string $serviceVersion Two formats are acceptable: 'v1' and '1'
     * @return string|bool Service name on success; false on failure
     */
    public function getServiceNameByOperation($operationName, $serviceVersion = null)
    {
        list($serviceName, $methodName) = $this->_parseOperationName($operationName);
        $serviceExists = isset($this->_data['services'][$serviceName]);
        if (!$serviceExists) {
            return false;
        }
        $serviceData = $this->_data['services'][$serviceName];
        $versionCheckRequired = is_string($serviceVersion);
        if ($versionCheckRequired) {
            /** Allow to take service version in two formats: with prefix and without it */
            $serviceVersion = is_numeric($serviceVersion)
                ? self::VERSION_NUMBER_PREFIX . $serviceVersion
                : ucfirst($serviceVersion);
            $operationIsValid = isset($serviceData['versions'][$serviceVersion]['methods'][$methodName]);
            if (!$operationIsValid) {
                return false;
            }
        }
        return $serviceName;
    }
}
