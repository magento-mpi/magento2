<?php
/**
 * Constraint callback option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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

        if (is_array($callable) && isset($callable[0]) && is_string($callable[0])) {
            if (!class_exists($callable[0])) {
                throw new InvalidArgumentException(sprintf('Class "%s" was not found', $callable[0]));
            }
            if ($this->_createInstance) {
//                echo __FILE__, PHP_EOL, __LINE__;
//                var_export($callable[0]);
//                die;
                $callable[0] = new $callable[0]();
            }
        } elseif ($this->_createInstance) {
            throw new InvalidArgumentException('Callable expected to be an array with class name as first element');
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
