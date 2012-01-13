<?php

/**
 * Request body JSON parser
 */
class Mage_Api2_Model_Request_Interpreter_Json implements Mage_Api2_Model_Request_Interpreter_Interface
{
    /**
     * Parse Request body into array of params
     *
     * @param $body
     * @param null|array $options
     * @return mixed
     */
    public function interpret($body, $options = null)
    {
        $data = Zend_Json::decode($body);

        return $data;
    }
}
