<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

class InputException extends AbstractAggregateException
{
    const INVALID_FIELD_RANGE = 'The %fieldName value of "%value" must be between %minValue and %maxValue';
    const INVALID_FIELD_MIN_VALUE = 'The %fieldName value of "%value" must be greater than or equal to %minValue.';
    const INVALID_FIELD_MAX_VALUE = 'The %fieldName value of "%value" must be less than or equal to %maxValue.';
    const INVALID_FIELD_VALUE = 'Invalid value of "%value" provided for the %fieldName field.';
    const REQUIRED_FIELD = '%fieldName is a required field.';

    /**
     * Initialize the input exception.
     *
     * @param string     $message Exception message
     * @param array      $params  Substitution parameters
     * @param \Exception $cause   Cause of the InputException
     */
    public function __construct(
        $message = 'One or more input exceptions have occurred.',
        $params = [],
        \Exception $cause = null
    ) {
        parent::__construct($message, $params, $cause);
    }
}
