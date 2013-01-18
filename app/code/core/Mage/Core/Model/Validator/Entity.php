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
class Mage_Core_Model_Validator_Entity implements Zend_Validate_Interface
{
    /**
     * Validation rules per scope (particular fields or entire entity)
     *
     * @var Zend_Validate[]
     */
    private $_rules = array();

    /**
     * Validation error messages
     *
     * @var array
     */
    private $_messages = array();

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
     * Check whether the entity is valid according to defined validation rules
     *
     * @param Varien_Object $entity
     * @return bool
     *
     * @throws Mage_Core_Exception
     */
    public function isValid(Varien_Object $entity)
    {
        $this->_messages = array();
        /** @var $validator Zend_Validate */
        foreach ($this->_rules as $fieldName => $validator) {
            $value = $fieldName ? $entity->getDataUsingMethod($fieldName) : $entity;
            if (!$validator->isValid($value)) {
                $this->_messages = array_merge($this->_messages, array_values($validator->getMessages()));
            }
        }
        return empty($this->_messages);
    }

    /**
     * Return error messages (if any) after the last validation
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
