<?php
interface Mage_Core_Model_Entity_Attribute_Interface
{
    public function getTypeCode();
    public function getValueFromTypeValues($typeValues);
    public function setConfig(Varien_Simplexml_Element $config);
    public function setType(Mage_Core_Model_Entity_Attribute_Type_Interface $type);
}