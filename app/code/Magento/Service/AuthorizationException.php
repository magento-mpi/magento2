<?php
/**
 * Services must throw this exception when encountering an unauthorized operation
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
        if ($userId && $resourceId) {
            $parameters = array_merge($parameters, array('user_id' => $userId, 'resource_id' => $resourceId));
            if (!$message) {
                $message = "User with ID '{$userId}' is not authorized to access resource with ID '{$resourceId}'.";
            }
        }
        parent::__construct($message, $code, $previous, $parameters);
    }
}
