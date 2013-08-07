<?php
/**
 * Validator constraint delegates validation of value's property to wrapped validator.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Validator_Constraint_Property extends Magento_Validator_Constraint
{
    /**
     * Property name
     *
     * @var string
     */
    protected $_property;

    /**
     * Constructor
     *
     * @param Magento_Validator_ValidatorInterface $validator
     * @param string $property
     * @param string $alias
     */
    public function __construct(Magento_Validator_ValidatorInterface $validator, $property, $alias = null)
    {
        parent::__construct($validator, $alias);
        $this->_property = $property;
    }

    /**
     * Get value that should be validated. Tries to extract value's property if Magento_Object or ArrayAccess or array
     * is passed
     *
     * @param mixed $value
     * @return mixed
     */
    protected function _getValidatorValue($value)
    {
        $result = null;

        if ($value instanceof Magento_Object) {
            $result = $value->getDataUsingMethod($this->_property);
        } elseif ((is_array($value) || $value instanceof ArrayAccess) && isset($value[$this->_property])) {
            $result = $value[$this->_property];
        }

        return $result;
    }

    /**
     * Add messages with code of property name
     *
     * @param array $messages
     */
    protected function _addMessages(array $messages)
    {
        $this->_messages[$this->_property] = $messages;
    }
}
