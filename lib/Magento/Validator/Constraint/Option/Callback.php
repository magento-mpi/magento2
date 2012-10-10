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
 * Constraint callback option
 */
class Magento_Validator_Constraint_Option_Callback implements Magento_Validator_Constraint_OptionInterface
{
    /**
     * @var callable
     */
    protected $_callable;

    /**
     * @var array
     */
    protected $_arguments;

    /**
     * @var bool
     */
    protected $_createInstance;

    /**
     * Create callback
     *
     * @param callable $callable
     * @param mixed $arguments
     * @param bool $createInstance If true than $callable[0] will be evaluated to new instance of class when get value
     */
    public function __construct($callable, $arguments = null, $createInstance = false)
    {
        $this->_callable = $callable;
        $this->setArguments($arguments);
        $this->_createInstance = $createInstance;
    }

    /**
     * Set callback arguments
     *
     * @param mixed $arguments
     */
    public function setArguments($arguments = null)
    {
        if (is_array($arguments)) {
            $this->_arguments = $arguments;
        } elseif (null !== $arguments) {
            $this->_arguments = array($arguments);
        } else {
            $this->_arguments = null;
        }
    }

    /**
     * Get callback value
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getValue()
    {
        $callable = $this->_callable;

        if (isset($callable[0]) && is_string($callable[0])
            && !Magento_Autoload::getInstance()->classExists($callable[0])
        ) {
            throw new InvalidArgumentException(sprintf('Class "%s" was not found', $callable[0]));
        }

        if ($this->_createInstance) {
            if (isset($callable[0]) && is_string($callable[0])) {
                $callable[0] = new $callable[0]();
            } else {
                throw new InvalidArgumentException('First element of callable expected to be class name');
            }
        }

        if (!is_callable($callable)) {
            throw new InvalidArgumentException('Callback does not callable');
        }

        if ($this->_arguments) {
            return call_user_func_array($callable, $this->_arguments);
        } else {
            return call_user_func($callable);
        }
    }
}
