<?php
/**
 * Interface of REST request content interpreter.
 *
 * @copyright {}
 */
interface Mage_Webapi_Controller_Request_Rest_InterpreterInterface
{
    /**
     * Parse request body into array of params.
     *
     * @param string $body Posted content from request
     * @return array|null Return NULL if content is invalid
     */
    public function interpret($body);
}
