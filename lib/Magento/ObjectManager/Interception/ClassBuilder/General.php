<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Interception\ClassBuilder;

class General
    implements \Magento\ObjectManager\Interception\ClassBuilder
{

    /**
     * Compose interceptor class name for the given class
     *
     * @param string $originalClassName
     * @return string
     */
    public function composeInterceptorClassName($originalClassName)
    {
        return $originalClassName . '_Interceptor';
    }
}
