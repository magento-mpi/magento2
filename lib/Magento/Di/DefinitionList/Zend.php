<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_DefinitionList_Zend extends Zend\Di\DefinitionList
{
    /**
     * Retrieve method parameters
     *
     * @param string $class
     * @param string $method
     * @return array
     */
    public function getMethodParameters($class, $method)
    {
        /** @var $definition \Zend\Di\Definition\DefinitionInterface */
        foreach ($this as $definition) {
            if ($definition->hasClass($class) && $definition->hasMethodParameters($class, $method)) {
                return $definition->getMethodParameters($class, $method);
            }
        }
        return array();
    }

    /**
     * Fix bug for in ZF2: https://github.com/zendframework/zf2/commit/26c8899ddfc4fe2672b2efa9ff3cf3cac600bec3
     * @todo Delete this method after ZF2 library update
     * {@inheritDoc}
     */
    public function hasMethod($class, $method)
    {
        /** @var $definition Zend\Di\Definition\DefinitionInterface */
        foreach ($this as $definition) {
            if ($definition->hasClass($class)) {
                if ($definition->hasMethods($class) === false
                    && $definition instanceof Zend\Di\Definition\PartialMarker
                ) {
                    continue;
                } else {
                    return $definition->hasMethod($class, $method);
                }
            }
        }

        return false;
    }
}
