<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity validator encapsulates validation rules both for individual entity fields and an entity as a whole
 */
class Mage_Core_Model_Validator_Entity
{
    /**
     * Validation rules per scope (particular fields or entire entity)
     *
     * @var Zend_Validate[]
     */
    private $_rules = array();

    /**
     * Add rule to be applied to a validation scope
     *
     * @param Zend_Validate_Interface $validator
     * @param string $fieldName Field name to apply validation to, or empty value to validate entity as a whole
     * @return Mage_Core_Model_Validator_Entity
     */
    public function addRule(Zend_Validate_Interface $validator, $fieldName = '')
    {
        if (!array_key_exists($fieldName, $this->_rules)) {
            $this->_rules[$fieldName] = new Zend_Validate();
        }
        $this->_rules[$fieldName]->addValidator($validator);
        return $this;
    }

    /**
     * Validate an entity according to defined validation rules
     *
     * @param Varien_Object $entity
     * @throws Mage_Core_Exception
     */
    public function validate(Varien_Object $entity)
    {
        $errors = array();
        /** @var $validator Zend_Validate_Interface */
        foreach ($this->_rules as $fieldName => $validator) {
            $value = $fieldName ? $entity->getDataUsingMethod($fieldName) : $entity;
            if (!$validator->isValid($value)) {
                $errors = array_merge($errors, array_values($validator->getMessages()));
            }
        }
        if ($errors) {
            $exception = new Mage_Core_Exception(implode(PHP_EOL, $errors));
            foreach ($errors as $errorMessage) {
                $exception->addMessage(new Mage_Core_Model_Message_Error($errorMessage));
            }
            throw $exception;
        }
    }
}
