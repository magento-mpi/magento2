<?php
/**
 * No such entity service exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

class NoSuchEntityException extends \Magento\Exception\LocalizedException
{
    const MESSAGE_SINGLE_FIELD = 'No such entity with %fieldName = %fieldValue';
    const MESSAGE_DOUBLE_FIELDS = 'No such entity with %fieldName = %fieldValue, %field2Name = %field2Value';

    /**
     * @param string $message
     * @param array $params
     * @param \Exception $cause
     */
    public function __construct(
        $message = 'No such entity.',
        array $params = [],
        \Exception $cause = null
    ) {
        parent::__construct($message, $params, $cause);
    }
}
