<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Validator
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Validator class that represents chain of validators.
 */
namespace Magento;

class Validator extends \Magento\Validator\ValidatorAbstract
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
     * @param \Magento\Validator\ValidatorInterface $validator
     * @param boolean $breakChainOnFailure
     * @return \Magento\Validator
     */
    public function addValidator(\Magento\Validator\ValidatorInterface $validator, $breakChainOnFailure = false)
    {
        if (!$validator->hasTranslator()) {
            $validator->setTranslator($this->getTranslator());
        }
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
     * @return boolean
     */
    public function isValid($value)
    {
        $result = true;
        $this->_clearMessages();

        /** @var $validator \Zend_Validate_Interface */
        foreach ($this->_validators as $element) {
            $validator = $element['instance'];
            if ($validator->isValid($value)) {
                continue;
            }
            $result = false;
            $this->_addMessages($validator->getMessages());
            if ($element['breakChainOnFailure']) {
                break;
            }
        }

        return $result;
    }

    /**
     * Set translator to chain.
     *
     * @param \Magento\Translate\AdapterInterface|null $translator
     * @return \Magento\Validator\ValidatorAbstract
     */
    public function setTranslator($translator = null)
    {
        foreach ($this->_validators as $validator) {
            $validator['instance']->setTranslator($translator);
        }
        return parent::setTranslator($translator);
    }
}
