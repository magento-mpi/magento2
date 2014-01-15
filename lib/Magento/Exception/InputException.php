<?php
/**
 * Input service exception
 *
 * The top level data (code and message) is consistent across all Input Exceptions.
 * InputException is inherently build to contain aggregates.  All failure specifics are stored in params.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

class InputException extends \Magento\Exception\Exception
{
    // This will always be the value of $this->code
    const INPUT_EXCEPTION = 0;

    // These are possible code to be stored in params
    const INVALID_FIELD_RANGE = 'INVALID_FIELD_RANGE';
    const INVALID_FIELD_VALUE = 'INVALID_FIELD_VALUE';
    const REQUIRED_FIELD = 'REQUIRED_FIELD';

    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct($message = 'One or more input exceptions have occurred.', $code = self::INPUT_EXCEPTION)
    {
        parent::__construct($message, $code);
    }

    /**
     * Create an input exception with the first error to be stored in params
     *
     * @param string $code
     * @param string $fieldName
     * @param string $value
     * @param array  $params
     * @return InputException
     */
    public static function create($code, $fieldName, $value, array $params = [])
    {
        $exception = new self();
        $exception->addError($code, $fieldName, $value, $params);
        return $exception;
    }

    /**
     * Add another error to the parameters list of errors
     *
     * @param string $code      Error code
     * @param string $fieldName Fieldname with bad input
     * @param string $value     Bad input value
     * @param array  $errorData Extra error debug data
     * @return $this
     */
    public function addError($code, $fieldName, $value, array $errorData = [])
    {
        $printParams = empty($errorData) ? "[]\n" : print_r($errorData, true);
        $this->message .= "\n{\n\tcode: $code\n\t$fieldName: $value\n\tparams: $printParams }\n";
        $errorData['fieldName'] = $fieldName;
        $errorData['code'] = $code;
        $errorData['value'] = $value;
        $this->_params[] = $errorData;
        return $this;
    }
}
