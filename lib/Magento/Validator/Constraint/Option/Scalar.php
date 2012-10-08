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
 * Constraint scalar option
 */
class Magento_Validator_Constraint_Option_Scalar implements  Magento_Validator_Constraint_OptionInterface
{
    /**
     * @var mixed
     */
    protected $_value;

    /**
     * Set value
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        if (is_array($value)) {
            $value = array_map('trim', $value);
        } elseif (is_string($value)) {
            $value = trim($value);
        }
        $this->_value = $value;
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }
}
