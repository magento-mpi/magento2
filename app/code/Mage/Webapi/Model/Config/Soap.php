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
     * Identify resource name by operation name.
     *
     * @param string $operationName
     * @return string|bool Resource name on success; false on failure
     */
    public function getResourceNameByOperation($operationName)
    {
        list($resourceName, $methodName) = $this->_parseOperationName($operationName);
        $resourceExists = isset($this->_data['resources'][$resourceName]);
        if (!$resourceExists) {
            return false;
        }
        $resourceData = $this->_data['resources'][$resourceName];
        $operationIsValid = isset($resourceData['methods'][$methodName]);
        if (!$operationIsValid) {
            return false;
        }
        return $resourceName;
    }
}
