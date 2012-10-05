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
 * Validator constraint for property
 */
class Magento_Validator_Constraint_Property extends Magento_Validator_Constraint
{
    /**
     * @var string
     */
    protected $_property;

    /**
     * Constructor
     *
     * @param Magento_Validator_Interface $validator
     * @param string $property
     * @param string $id
     */
    public function __construct(Magento_Validator_Interface $validator, $property, $id = null)
    {
        parent::__construct($validator, $id);
        $this->_property = $property;
    }

    /**
     * Add messages from validator
     *
     * @param Magento_Validator_Interface $validator
     */
    protected function _addMessages($validator)
    {
        foreach ($validator->getMessages() as $message) {
            $this->_messages[$this->_property][] = $message;
        }
    }

    /**
     * Get value that should be validated.
     *
     * @param Varien_Object|array $value
     * @return mixed
     */
    protected function _getValidatorValue($value)
    {
        $result = null;
        if ($value instanceof Varien_Object) {
            return $result = $value->getDataUsingMethod($this->_property);
        } elseif ((is_array($value) || $value instanceof ArrayAccess) && isset($value[$this->_property])) {
            return  $result = $value[$this->_property];
        }

        return $result;
    }
}
