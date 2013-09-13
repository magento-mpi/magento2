<?php
/**
 * Services must throw this exception when encountering an unautorization operation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Service_AuthorizationException extends Magento_Service_Exception
{
    /**
     * Create custom message for authorization exception.
     *
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @param array $parameters
     * @param string|int|null $userId
     * @param string|int|null $resourceId
     */
    public function __construct(
        $message = '',
        // TODO Specify default exception code when Service Exception Handling policy is defined
        $code = 0,
        Exception $previous = null,
        $parameters = array(),
        $userId = null,
        $resourceId = null
    ) {
        if (!$message && $userId && $resourceId) {
            $message = "User with ID '{$userId}' is not authorized to access resource with ID '{$resourceId}'.";
        }
        parent::__construct($message, $code, $previous, $parameters);
    }
}
