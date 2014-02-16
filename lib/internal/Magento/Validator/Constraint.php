<?php
/**
 * Validator constraint delegates validation to wrapped validator.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Validator;

class Constraint extends \Magento\Validator\AbstractValidator
{
    /**
     * Wrapped validator
     *
     * @var \Magento\Validator\ValidatorInterface
     */
    protected $_wrappedValidator;

    /**
     * Alias can be used for search
     *
     * @var string
     */
    protected $_alias;

    /**
     * Constructor
     *
     * @param \Magento\Validator\ValidatorInterface $validator
     * @param string $alias
     */
    public function __construct(\Magento\Validator\ValidatorInterface $validator, $alias = null)
    {
        $this->_wrappedValidator = $validator;
        $this->_alias = $alias;
    }

    /**
     * Delegate validation to wrapped validator
     *
     * @param mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $result = true;
        $this->_clearMessages();

        if (!$this->_wrappedValidator->isValid($this->_getValidatorValue($value))) {
            $this->_addMessages($this->_wrappedValidator->getMessages());
            $result = false;
        }

        return $result;
    }

    /**
     * Get value that should be validated.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function _getValidatorValue($value)
    {
        if (is_array($value)) {
            $value = new \Magento\Object($value);
        }
        return $value;
    }

    /**
     * Get constraint alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->_alias;
    }

    /**
     * Set translator to wrapped validator.
     *
     * @param \Magento\Translate\AdapterInterface|null $translator
     * @return \Magento\Validator\AbstractValidator
     */
    public function setTranslator($translator = null)
    {
        $this->_wrappedValidator->setTranslator($translator);
        return $this;
    }

    /**
     * Get translator instance of wrapped validator
     *
     * @return \Magento\Translate\AdapterInterface|null
     */
    public function getTranslator()
    {
        return $this->_wrappedValidator->getTranslator();
    }
}
