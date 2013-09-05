<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Generator;

class ClassGenerator
{
    /**
     * @var \Magento\Code\Generator
     */
    protected $_generator;

    /**
     * @param \Magento\Code\Generator $generator
     */
    public function __construct(\Magento\Code\Generator $generator = null)
    {
        $this->_generator = $generator ?: new \Magento\Code\Generator();
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
        $reflectionClass = new \ReflectionClass($className);
        if ($reflectionClass->hasMethod('__construct')) {
            $constructor = $reflectionClass->getMethod('__construct');
            $parameters = $constructor->getParameters();
            /** @var $parameter \ReflectionParameter */
            foreach ($parameters as $parameter) {
                preg_match('/\[\s\<\w+?>\s([\w\\\\]+)/s', $parameter->__toString(), $matches);
                if (isset($matches[1])) {
                    $this->_generator->generateClass($matches[1]);
                }
            }
        }
    }
}
