<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Di\Code\Scanner;

class InheritanceInterceptorScanner implements ScannerInterface
{
    /**
     * Get intercepted class names
     *
     * @param array $classes
     * @param array $interceptedEntities
     * @return array
     */
    public function collectEntities(array $classes, array $interceptedEntities = array())
    {
        $output = array();
        foreach ($classes as $class) {
            foreach ($interceptedEntities as $interceptorClass) {
                $interceptedEntity = substr($interceptorClass, 0, -12);
                if (is_subclass_of($class, $interceptedEntity)) {
                    $reflectionClass = new \ReflectionClass($class);
                    if (!$reflectionClass->isAbstract() && !$reflectionClass->isFinal()) {
                        $output[] = $class . '\\Interceptor';
                    }
                }
            }
        }
        $output = array_merge($interceptedEntities, $output);
        $output = array_unique($output);
        return $output;
    }
}
