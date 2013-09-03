<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Interception\ClassBuilder;

class Runtime
    implements \Magento\ObjectManager\Interception\ClassBuilder
{
    /**
     * Class generator
     *
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
     * Compose interceptor class name for the given class
     *
     * @param string $originalClassName
     * @return string
     */
    public function composeInterceptorClassName($originalClassName)
    {
        $className = $originalClassName . '_Interceptor';
        if (!class_exists($className)) {
            $this->_generator->generateClass($className);
        }
        return $className;
    }
}
