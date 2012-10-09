<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Zend\Di\Exception,
    Zend\Code\Reflection;

class Magento_Di_Definition_CompilerDefinition_Zend extends Zend\Di\Definition\CompilerDefinition
    implements Magento_Di_Definition_CompilerDefinition
{
    /**
     * Process class method parameters
     *
     * @param array $def
     * @param Zend\Code\Reflection\ClassReflection $rClass
     * @param Zend\Code\Reflection\MethodReflection $rMethod
     */
    protected function processParams(&$def, Reflection\ClassReflection $rClass, Reflection\MethodReflection $rMethod)
    {
        if (count($rMethod->getParameters()) === 0) {
            return;
        }

        parent::processParams($def, $rClass, $rMethod);

        $methodName = $rMethod->getName();

        /** @var $p \ReflectionParameter */
        foreach ($rMethod->getParameters() as $p) {
            $fqName = $rClass->getName() . '::' . $rMethod->getName() . ':' . $p->getPosition();

            $def['parameters'][$methodName][$fqName][] = ($p->isOptional() && $p->isDefaultValueAvailable())
                ? $p->getDefaultValue()
                : null;
        }
    }

    /**
     * Get definition as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->toArrayDefinition()->toArray();
    }
}
