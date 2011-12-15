<?php

class Mage_Api2_Model_Request_Interpreter_Xml implements Mage_Api2_Model_Request_Interpreter_Interface
{
    public function interpret($body, $options = null)
    {
        $xml = simplexml_load_string($body);
        $data = Mage_Api2_Helper_Data::simpleXmlToArray($xml);

        return $data;
    }
}
