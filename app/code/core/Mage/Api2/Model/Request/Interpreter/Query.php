<?php

/**
 * Request body query parsers
 */
class Mage_Api2_Model_Request_Interpreter_Query implements Mage_Api2_Model_Request_Interpreter_Interface
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
        $data = array();
        parse_str($body, $data);

        return $data;
    }
}
