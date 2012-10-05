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
 * Validator class.
 */
class Magento_Validator extends Magento_Validator_Abstract
{
    /**
     * Validator chain
     *
     * @var array
     */
    protected $_validators = array();

    /**
     * Adds a validator to the end of the chain
     *
     * @param  Magento_Validator_Interface $validator
     * @param  bool $breakChainOnFailure
     * @return Magento_Validator
     */
    public function addValidator(Magento_Validator_Interface $validator, $breakChainOnFailure = false)
    {
        $this->_validators[] = array(
            'instance' => $validator,
            'breakChainOnFailure' => (boolean)$breakChainOnFailure
        );
        return $this;
    }

    /**
     * Returns true if and only if $value passes all validations in the chain
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $result = true;
        $this->_clearMessages();

        /** @var $validator Zend_Validate_Interface */
        foreach ($this->_validators as $element) {
            $validator = $element['instance'];
            if ($validator->isValid($value)) {
                continue;
            }
            $result = false;
            $this->_addMessages($validator);
            if ($element['breakChainOnFailure']) {
                break;
            }
        }

        return $result;
    }
}
