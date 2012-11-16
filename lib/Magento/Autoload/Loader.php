<?php
/**
 * An autoloader that uses callback as a locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Autoload_Loader
{
    /**
     * @var callable
     */
    private $_locator = null;

    /**
     * Register specified callback as locator for getting files
     *
     * @param callable $locatorCallback
     * @throws InvalidArgumentException
     */
    public function __construct($locatorCallback)
    {
        if (!is_callable($locatorCallback)) {
            throw new InvalidArgumentException('Provided argument is not a callback.');
        }
        $this->_locator = $locatorCallback;
    }

    /**
     * Locate a file by provided class name and include it
     *
     * @param string $class
     */
    public function load($class)
    {
        $file = call_user_func_array($this->_locator, array($class));
        if ($file && file_exists($file)) {
            include $file;
        }
    }

    /**
     * Put itself to the top of autoload stack
     */
    public function register()
    {
        spl_autoload_register(array($this, 'load'));
    }
}
