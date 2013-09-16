<?php
/**
 * Services must throw this exception when not able to locate a resource including lookup failure
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Service_ResourceNotFoundException extends Magento_Service_Exception
{
    /**
     * Create custom message for resource not found exception.
     *
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @param array $parameters
     * @param string|int|null $resourceId
     */
    public function __construct(
        $message = '',
        // TODO Specify default exception code when Service Exception Handling policy is defined
        $code = 0,
        Exception $previous = null,
        $parameters = array(),
        $resourceId = null
    ) {
        if (!$message && $resourceId) {
            $message = "Resource with ID '{$resourceId}' not found.";
        }
        parent::__construct($message, $code, $previous, $parameters);
    }
}
