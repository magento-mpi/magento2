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
    const INVALID_FIELD_RANGE = 'Invalid field range.';
    const INVALID_FIELD_VALUE = 'Invalid field value.';
    const INVALID_STATE_CHANGE = 'Invalid state change.';
    const EMPTY_FIELD_REQUIRED = 'A required field has been left empty.';
    const NO_SUCH_ENTITY = 'No such entity found.';
    const TOKEN_EXPIRED = 'The token has expired.';

    /**
     * Create an input exception with the first error to be stored in params
     *
     * @param string $fieldName
     * @param string $code
     * @param array  $params
     * @return self
     */
    public static function create($fieldName, $code, array $params = array())
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
    public function addError($fieldName, $code, array $params = array())
    {
        $this->message .= "\n fieldName: $fieldName, code: $code, params: " . print_r($params, true);
        $params['fieldName'] = $fieldName;
        $params['code'] = $code;
        $this->params[] = $params;
        return $this;
    }
}
