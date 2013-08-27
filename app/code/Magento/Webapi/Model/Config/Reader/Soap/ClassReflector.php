<?php
/**
 * SOAP API specific class reflector.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Config_Reader_Soap_ClassReflector
    extends Magento_Webapi_Model_Config_Reader_ClassReflectorAbstract
{
    /**
     * Set types data into reader after reflecting all files.
     *
     * @return array
     */
    public function getPostReflectionData()
    {
        return array(
            'types' => $this->_typeProcessor->getTypesData(),
            'type_to_class_map' => $this->_typeProcessor->getTypeToClassMap(),
        );
    }
}
