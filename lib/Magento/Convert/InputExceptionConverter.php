<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
namespace Magento\Convert;

use Magento\Exception\InputException;

/**
 * Class InputExceptionConverter converts an InputException into a string
 */
class InputExceptionConverter
{
    public function toString(InputException $inputException)
    {
        $message = '';

        foreach ($inputException->getParams() as $error) {
            $message .= $this->convertErrorToMessage($error) . ' ';
        }

        return $message;
    }

    /**
     * @param array $error a map of string keys to mixed values.
     * @return string
     */
    public function convertErrorToMessage($error)
    {
        switch ($error['code']) {
            case InputException::INVALID_FIELD_VALUE:
            case InputException::INVALID_FIELD_RANGE:
                $message = __('Invalid value of "%1" provided for %2 field.', $error['value'], $error['fieldName']);
                break;
            case InputException::REQUIRED_FIELD:
                $message = __('%1 is a required field.', $error['fieldName']);
                break;
            default:
                $message = __('Unknown Error.');
                break;
        }
        return $message;
    }
}
