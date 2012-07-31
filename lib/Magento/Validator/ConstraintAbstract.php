<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Validator constraint interface.
 */
abstract class Magento_Validator_ConstraintAbstract
{
    protected $_errors = array();

    /**
     * Validate field value in data.
     *
     * @param array $data
     * @param string $field
     * @return boolean
     */
    abstract public function isValidData(array $data, $field = null);

    /**
     * Get constraint error messages.
     * Errors should be stored in associative array grouped by field name, e.g.
     * array(
     *     'field_name_1' => array(
     *          'Error message #1',
     *          'Error message #2',
     *          ...
     *      ),
     *      'field_name_2' => array(
     * )
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Add error message
     *
     * @param string $field
     * @param string $message
     */
    public function addError($field, $message)
    {
        $this->_errors[$field][] = $message;
    }
}
