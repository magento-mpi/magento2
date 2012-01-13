<?php

/**
 * Request body XML parser
 */
class Mage_Api2_Model_Request_Interpreter_Xml implements Mage_Api2_Model_Request_Interpreter_Interface
{
    /**
     * Parse Request body into array of params
     *
     * @param string $body
     * @param null|array $options
     * @return array
     */
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
