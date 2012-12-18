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
     * Fix bug in ZF2
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
