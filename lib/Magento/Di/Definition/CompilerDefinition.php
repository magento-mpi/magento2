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

class Magento_Di_Definition_CompilerDefinition extends Zend\Di\Definition\CompilerDefinition
{
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
}
