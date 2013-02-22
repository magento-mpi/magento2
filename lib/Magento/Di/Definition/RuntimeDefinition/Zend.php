<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Zend\Code\Reflection,
    Zend\Di\Definition\IntrospectionStrategy;

class Magento_Di_Definition_RuntimeDefinition_Zend extends Zend\Di\Definition\RuntimeDefinition
    implements Magento_Di_Definition_RuntimeDefinition
{
    /**
     * @var Magento_Di_Generator_Class
     */
    protected $_classGenerator;

    /**
     * @param Zend\Di\Definition\IntrospectionStrategy $strategy
     * @param array $explicitClasses
     */
    public function __construct(IntrospectionStrategy $strategy = null, array $explicitClasses = null)
    {
        parent::__construct($strategy, $explicitClasses);
    }

    /**
     * Process method parameters
     *
     * @param array $def
     * @param Reflection\ClassReflection $rClass
     * @param Reflection\MethodReflection $rMethod
     */
    protected function processParams(&$def, Reflection\ClassReflection $rClass, Reflection\MethodReflection $rMethod)
    {
        if (count($rMethod->getParameters()) === 0) {
            return;
        }

        $methodName = $rMethod->getName();

        // @todo annotations here for alternate names?

        $def['parameters'][$methodName] = array();

        foreach ($rMethod->getParameters() as $p) {

            /** @var $p \ReflectionParameter  */
            $actualParamName = $p->getName();

            $fqName = $rClass->getName() . '::' . $rMethod->getName() . ':' . $p->getPosition();

            $def['parameters'][$methodName][$fqName] = array();

            // set the class name, if it exists
            $def['parameters'][$methodName][$fqName][] = $actualParamName;
            $def['parameters'][$methodName][$fqName][] = ($p->getClass() !== null) ? $p->getClass()->getName() : null;
            $def['parameters'][$methodName][$fqName][] = !$p->isOptional();
            $def['parameters'][$methodName][$fqName][] = ($p->isOptional() && $p->isDefaultValueAvailable())
                ? $p->getDefaultValue()
                : null;
        }

    }

    /**
     * Check whether method has parameters
     *
     * @param string $class
     * @param string $method
     * @return mixed
     */
    public function getMethodParameters($class, $method)
    {
        if (!array_key_exists($class, $this->classes)) {
            $this->processClass($class);
        }

        return isset($this->classes[$class]['parameters'][$method])
            ? $this->classes[$class]['parameters'][$method]
            : null;
    }
}
