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
     * @var array
     */
    protected $_callback;

    /**
     * @var mixed
     */
    protected $_arguments = null;

    /**
     * Create callback
     *
     * @param string $class
     * @param string $method
     */
    public function __construct($class, $method)
    {
        if (is_string($class)) {
            $class = trim($class);
        }
        $this->_callback = array($class, trim($method));
    }

    /**
     * Set callback arguments
     *
     * @param mixed $arguments
     */
    public function setArguments($arguments)
    {
        $this->_arguments = $arguments;
    }

    /**
     * Get callback value
     *
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function getValue()
    {
        if (is_string($this->_callback[0])) {
            $autoLoader = Magento_Autoload::getInstance();
            $callbackClass = $this->_callback[0];
            if (!$autoLoader->classExists($callbackClass)) {
                throw new InvalidArgumentException(sprintf('Class "%s" was not found', $callbackClass));
            }
            $this->_callback[0] = new $callbackClass();
        }
        if (!is_callable($this->_callback)) {
            throw new InvalidArgumentException('Callback does not callable');
        }
        if (is_array($this->_arguments)) {
            return call_user_func_array($this->_callback, $this->_arguments);
        } elseif ($this->_arguments) {
            return call_user_func($this->_callback, $this->_arguments);
        } else {
            return call_user_func($this->_callback);
        }
    }
}
