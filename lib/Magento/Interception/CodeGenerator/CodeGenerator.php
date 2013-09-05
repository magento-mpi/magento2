<?php
/**
 * Interceptor generator. Used to automatically create Interception classes for intercepted classes
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Interception_CodeGenerator_CodeGenerator
    implements Magento_Interception_CodeGenerator
{
    /**
     * Class generator
     *
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
     * {@inheritdoc}
     */
    public function generate($interceptorClass)
    {
        $this->_generator->generateClass($interceptorClass);
    }
}
