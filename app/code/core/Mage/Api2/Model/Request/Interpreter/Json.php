<?php

class Mage_Apia_Model_Request_Interpreter_Json implements Mage_Apia_Model_Request_Interpreter_Interface
{
    public function interpret($body, $options = null)
    {
        $data = Zend_Json::decode($body);

        return $data;
    }
}
