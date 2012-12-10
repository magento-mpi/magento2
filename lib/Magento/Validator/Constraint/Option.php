<?php
/**
 * Constraint option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Validator_Constraint_Option implements  Magento_Validator_Constraint_OptionInterface
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
