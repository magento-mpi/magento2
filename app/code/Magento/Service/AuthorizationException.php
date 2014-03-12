<?php
/**
 * Services must throw this exception when encountering an unauthorized operation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service;

class AuthorizationException extends \Magento\Service\Exception
{
    /**
     * Create custom message for authorization exception.
     *
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     * @param array $parameters
     * @param string $name
     * @param string|int|null $userId
     * @param string|int|null $resourceId
     */
    public function __construct(
        $message = '',
        $code = 0,
        \Exception $previous = null,
        array $parameters = array(),
        $name = 'authorization',
        $userId = null,
        $resourceId = null
    ) {
        if ($userId && $resourceId) {
            $parameters = array_merge($parameters, array('user_id' => $userId, 'resource_id' => $resourceId));
            if (!$message) {
                $message = "User with ID '{$userId}' is not authorized to access resource with ID '{$resourceId}'.";
            }
        }
        parent::__construct($message, $code, $previous, $parameters, $name);
    }
}
