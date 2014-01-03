<?php
/**
 * Input service exception
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
    const INPUT_EXCEPTION = 0;

    const INVALID_FIELD_RANGE = 'Invalid field range.';
    const INVALID_STATE_CHANGE = 'Invalid state change.';
    const EMPTY_FIELD_REQUIRED = 'A required field has been left empty.';
    const NO_SUCH_ENTITY = 'No such entity found.';

    /**
     * @param string $fieldName
     * @param string $code
     * @param array  $params
     */
    public function __construct($fieldName, $code, array $params = array())
    {
        parent::__construct('One or more input exceptions have occurred.', self::INPUT_EXCEPTION);
        $this->addError($fieldName, $code, $params);
    }

    /**
     * @param string $fieldName
     * @param string $code
     * @param array  $params
     */
    public function addError($fieldName, $code, array $params = array())
    {
        $params['fieldName'] = $fieldName;
        $params['code'] = $code;
        $this->params[] = $params;
    }
}
