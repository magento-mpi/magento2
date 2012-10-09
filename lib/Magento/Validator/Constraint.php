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
 * Validator constraint delegates validation to wrapped validator.
 */
class Magento_Validator_Constraint extends Magento_Validator_ValidatorAbstract
{
    /**
     * Wrapped validator
     *
     * @var Magento_Validator_ValidatorInterface
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
     * @param Magento_Validator_ValidatorInterface $validator
     * @param string $alias
     */
    public function __construct(Magento_Validator_ValidatorInterface $validator, $alias = null)
    {
        $this->_wrappedValidator = $validator;
        $this->_alias = $alias;
    }

    /**
     * Delegate validation to wrapped validator
     *
     * @param mixed $value
     * @return bool
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
}
