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
 * Validator constraint
 */
class Magento_Validator_Constraint extends Magento_Validator_Abstract
{
    /**
     * @var string
     */
    protected $_id;

    /**
     * @var Magento_Validator_Interface
     */
    protected $_wrappedValidator;

    /**
     * Constructor
     *
     * @param Magento_Validator_Interface $validator
     * @param string $id
     */
    public function __construct(Magento_Validator_Interface $validator, $id = null)
    {
        $this->_id = $id;
        $this->_wrappedValidator = $validator;
    }

    /**
     * Get constraint id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Check constraint validator is valid.
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $result = true;
        $this->_clearMessages();

        if (!$this->_wrappedValidator->isValid($this->_getValidatorValue($value))) {
            $this->_addMessages($this->_wrappedValidator);
            $result = false;
        }

        return $result;
    }

    /**
     * Add messages from validator
     *
     * @param Magento_Validator_Interface $validator
     */
    protected function _addMessages($validator)
    {
        $this->_messages = $validator->getMessages();
    }

    /**
     * Get value that should be validated.
     *
     * @param mixed $value
     * @return bool
     */
    protected function _getValidatorValue($value)
    {
        return $value;
    }
}
