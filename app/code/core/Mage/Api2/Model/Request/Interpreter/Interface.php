<?php

/**
 * Interface for Request body parsers
 */
interface Mage_Api2_Model_Request_Interpreter_Interface
{
    /**
     * Parse Request body into array of params
     *
     * @abstract
     * @param string $body
     * @return array
     */
    public function interpret($body);
}
