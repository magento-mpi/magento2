<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_ObjectManager_Interception_ClassBuilder_Runtime
    implements Magento_ObjectManager_Interception_ClassBuilder
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
