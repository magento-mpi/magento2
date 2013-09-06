<?php
/**
 * Interceptor generator. Used to automatically create Interception classes for intercepted classes
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\CodeGenerator;

class CodeGenerator
    implements \Magento\Interception\CodeGenerator
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
     * {@inheritdoc}
     */
    public function generate($interceptorClass)
    {
        $this->_generator->generateClass($interceptorClass);
    }
}
