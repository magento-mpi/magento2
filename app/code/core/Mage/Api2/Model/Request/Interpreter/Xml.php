<?php

class Mage_Api2_Model_Request_Interpreter_Xml implements Mage_Api2_Model_Request_Interpreter_Interface
{
    public function interpret($body, $options = null)
    {
        /*$xml = simplexml_load_string($body);
        $data = Mage_Api2_Helper_Data::simpleXmlToArray($xml);*/

        $body = strstr($body, '<?xml')  ?$body  :'<?xml version="1.0"?>'.PHP_EOL.$body;
        $xml = new Zend_Config_Xml($body);
        $data = $xml->toArray();

        return $data;
    }
}
