<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Code_Generator_Class
{
    /**
     * @var Magento_Code_Generator
     */
    protected $_generator;

    /**
     * @param Magento_Code_Generator $generator
     */
    public function __construct(Magento_Code_Generator $generator = null)
    {
        $this->_generator = $generator ?: new Magento_Code_Generator();
    }

    /**
     * Generate all not existing entity classes in constructor
     *
     * @param string $className
     */
    public function generateForConstructor($className)
    {
        if (!class_exists($className)) {
            $this->_generator->generateClass($className);
        }
        $reflectionClass = new ReflectionClass($className);
        if ($reflectionClass->hasMethod('__construct')) {
            $constructor = $reflectionClass->getMethod('__construct');
            $parameters = $constructor->getParameters();
            /** @var $parameter ReflectionParameter */
            foreach ($parameters as $parameter) {
                preg_match('/\[\s\<\w+?>\s([\w\\\\]+)/s', $parameter->__toString(), $matches);
                if (isset($matches[1])) {
                    $this->_generator->generateClass($matches[1]);
                }
            }
        }
    }
}
