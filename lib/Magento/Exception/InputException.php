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
    const INVALID_STATE_CHANGE = 'INVALID_STATE_CHANGE';
    // FIXME: EMPTY_FIELD_REQUIRED is could be read to mean the field is required to be empty. Suggest: REQUIRED_FIELD
    const EMPTY_FIELD_REQUIRED = 'REQUIRED_FIELD';
    const NO_SUCH_ENTITY = 'NO_SUCH_ENTITY';
    const TOKEN_EXPIRED = 'TOKEN_EXPIRED';
    const DUPLICATE_UNIQUE_VALUE_EXISTS = 'DUPLICATE_UNIQUE_VALUE_EXISTS';

    /**
     * Create an input exception with the first error to be stored in params
     *
     * @param string $fieldName
     * @param string $code
     * @param array  $params
     * @return self
     */
    public static function create($fieldName, $code, array $params =[])
    {
        $exception = new self();
        $exception->addError($fieldName, $code, $params);
        return $exception;
    }

    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct($message = 'One or more input exceptions have occurred.', $code = self::INPUT_EXCEPTION)
    {
        parent::__construct($message, $code);
    }

    /**
     * Add another error to the parameters list of errors
     *
     * @param string $fieldName
     * @param string $code
     * @param array  $params
     *
     * @return $this
     */
    public function addError($fieldName, $code, array $params = [])
    {
        $printParams = empty($params) ? "[]\n" : print_r($params, true);
        $this->message .= "\n{\n\tfieldName: $fieldName\n\tcode: $code\n\tparams: " . $printParams . "}\n";
        $params['fieldName'] = $fieldName;
        $params['code'] = $code;
        $this->params[] = $params;
        return $this;
    }
}
