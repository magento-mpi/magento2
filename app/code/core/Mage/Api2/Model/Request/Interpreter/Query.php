<?php

class Mage_Api2_Model_Request_Interpreter_Query implements Mage_Api2_Model_Request_Interpreter_Interface
{
    public function interpret($body, $options = null)
    {
        $data = array();
        parse_str($body, $data);

        return $data;
    }
}
