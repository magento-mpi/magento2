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
 * Presentation layer validator class.
 */
class Magento_Validator
{
    /** @var string */
    protected $_entityName;
    /** @var string */
    protected $_groupName;
    /** @var Magento_Validator_Config */
    protected $_config;
    /** @var array */
    protected $_messages = array();

    /**
     * Set validation entity and group names, load validator config.
     *
     * @param string $entityName
     * @param string $groupName
     * @param Magento_Validator_Config $config
     * @throws InvalidArgumentException
     */
    public function __construct($entityName, $groupName, Magento_Validator_Config $config)
    {
        if (!$entityName) {
            throw new InvalidArgumentException('Validation entity name is required.');
        }
        $this->_entityName = $entityName;

        if (!$groupName) {
            throw new InvalidArgumentException('Validation group name is required.');
        }
        $this->_groupName = $groupName;

        $this->_config = $config;
    }

    /**
     * Validate input data against validation rules, defined in config group.
     *
     * @param array $data
     * @throws Magento_Exception
     * @return bool
     */
    public function isValid(array $data)
    {
        $isValid = true;
        $rules = $this->_config->getValidationRules($this->_entityName, $this->_groupName);
        foreach ($rules as $rule) {
            foreach ($rule as $constraintConfig) {
                $constraint = $constraintConfig['constraint'];
                $field = isset($constraintConfig['field']) ? $constraintConfig['field'] : null;
                if ($constraint instanceof Zend_Validate_Interface) {
                    /** @var Zend_Validate_Interface $constraint */
                    $value = isset($data[$field]) ? $data[$field] : null;
                    if (!$constraint->isValid($value)) {
                        foreach ($constraint->getMessages() as $error) {
                            $this->_messages[$field][] = $error;
                        }
                        $isValid = false;
                    }
                } else {
                    /** @var Magento_Validator_ConstraintAbstract $constraint */
                    if (!$constraint->isValidData($data, $field)) {
                        foreach ($constraint->getErrors() as $errorFieldName => $errors) {
                            foreach ($errors as $error) {
                                $this->_messages[$errorFieldName][] = $error;
                            }
                        }
                        $isValid = false;
                    }
                }
            }
        }

        return $isValid;
    }

    /**
     * Get validation messages.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
