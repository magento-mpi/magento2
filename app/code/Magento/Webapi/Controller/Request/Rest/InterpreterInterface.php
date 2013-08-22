<?php
/**
 * Interface of REST request content interpreter.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Webapi_Controller_Request_Rest_InterpreterInterface
{
    /**
     * Parse request body into array of params.
     *
     * @param string $body Posted content from request
     * @return array|null Return NULL if content is invalid
     */
    public function interpret($body);
}
