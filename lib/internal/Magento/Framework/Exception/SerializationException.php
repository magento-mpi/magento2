<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Exception;

/**
 * Serialization Exception
 */
class SerializationException extends LocalizedException
{
    const TYPE_MISMATCH = 'Invalid type for value :"%value". Expected Type: "%type".';

    /**
     * @param string     $message
     * @param array      $params
     * @param \Exception $cause
     */
    public function __construct($message = self::TYPE_MISMATCH, array $params = [], \Exception $cause = null)
    {
        parent::__construct($message, $params, $cause);
    }
}