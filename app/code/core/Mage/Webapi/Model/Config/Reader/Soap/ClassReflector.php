<?php
/**
 * SOAP API specific class reflector.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Config_Reader_Soap_ClassReflector
    extends Mage_Webapi_Model_Config_Reader_ClassReflectorAbstract
{
    /**
     * Set types data into reader after reflecting all files.
     */
    public function afterReflectionAction()
    {
        $this->getReader()->addData(array(
            'types' => $this->_typeProcessor->getTypesData(),
            'type_to_class_map' => $this->_typeProcessor->getTypeToClassMap(),
        ));
    }
}
