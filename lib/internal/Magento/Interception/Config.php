<?php
/**
 * Interception config. Tells whether plugins have been added for type.
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception;

interface Config
{
    const BEFORE_SCENARIO = 1;
    const AFTER_SCENARIO = 2;
    const AROUND_SCENARIO = 3;

    /**
     * Check whether type has configured plugins
     *
     * @param string $type
     * @return bool
     */
    public function hasPlugins($type);

    /**
     * Generate interceptor class name
     *
     * @param string $type
     * @return string
     */
    public function getInterceptorClassName($type);
}
