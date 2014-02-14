<?php
/**
 * Interceptor class generator
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception;

interface CodeGenerator
{
    /**
     * Generate interceptor class name
     *
     * @param string $interceptorClassName
     */
    public function generate($interceptorClassName);
}
