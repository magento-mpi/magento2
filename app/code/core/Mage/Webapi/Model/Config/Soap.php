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
     * @param Mage_Core_Model_App $app
     */
    public function __construct(
        Mage_Webapi_Model_Config_Reader_Soap $reader,
        Mage_Webapi_Helper_Config $helper,
        Mage_Core_Model_App $app
    ) {
        parent::__construct($reader, $helper, $app);
    }

    /**
     * Retrieve specific resource version interface data.
     *
     * Perform metadata merge from previous method versions.
     *
     * @param string $resourceName
     * @param string $resourceVersion Two formats are acceptable: 'v1' and '1'
     * @return array
     * @throws RuntimeException
     */
    public function getResourceDataMerged($resourceName, $resourceVersion)
    {
        /** Allow to take resource version in two formats: with prefix and without it */
        $resourceVersion = is_numeric($resourceVersion)
            ? self::VERSION_NUMBER_PREFIX . $resourceVersion
            : ucfirst($resourceVersion);
        $this->_checkIfResourceVersionExists($resourceName, $resourceVersion);
        $resourceData = array();
        foreach ($this->_data['resources'][$resourceName]['versions'] as $version => $data) {
            $resourceData = array_replace_recursive($resourceData, $data);
            if ($version == $resourceVersion) {
                break;
            }
        }
        return $resourceData;
    }

    /**
     * Identify resource name by operation name.
     *
     * If $resourceVersion is set, the check for operation validity in specified resource version will be performed.
     * If $resourceVersion is not set, the only check will be: if resource exists.
     *
     * @param string $operationName
     * @param string $resourceVersion Two formats are acceptable: 'v1' and '1'
     * @return string|bool Resource name on success; false on failure
     */
    public function getResourceNameByOperation($operationName, $resourceVersion = null)
    {
        list($resourceName, $methodName) = $this->_parseOperationName($operationName);
        $resourceExists = isset($this->_data['resources'][$resourceName]);
        if (!$resourceExists) {
            return false;
        }
        $resourceData = $this->_data['resources'][$resourceName];
        $versionCheckRequired = is_string($resourceVersion);
        if ($versionCheckRequired) {
            /** Allow to take resource version in two formats: with prefix and without it */
            $resourceVersion = is_numeric($resourceVersion)
                ? self::VERSION_NUMBER_PREFIX . $resourceVersion
                : ucfirst($resourceVersion);
            $operationIsValid = isset($resourceData['versions'][$resourceVersion]['methods'][$methodName]);
            if (!$operationIsValid) {
                return false;
            }
        }
        return $resourceName;
    }
}
