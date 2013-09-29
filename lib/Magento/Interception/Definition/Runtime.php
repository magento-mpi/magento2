<?php
/**
 * \Reflection based plugin method list. Uses reflection to retrieve list of interception methods defined in plugin.
 * Should be only used in development mode, because it reads method list on every request which is expensive.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Definition;

class Runtime implements \Magento\Interception\Definition
{
    /**
     * Retrieve list of methods
     *
     * @param string $type
     * @return array
     */
    public function getMethodList($type)
    {
        return get_class_methods($type);
    }
}
