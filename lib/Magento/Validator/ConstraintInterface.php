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
interface Magento_Validator_ConstraintInterface
{
    /**
     * Validate field value in data.
     *
     * @param array $data
     * @param string $field
     * @return boolean
     */
    public function isValidData(array $data, $field = null);

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
    public function getErrors();
}