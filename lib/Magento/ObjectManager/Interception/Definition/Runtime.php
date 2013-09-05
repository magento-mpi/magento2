<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager\Interception\Definition;

class Runtime implements \Magento\ObjectManager\Interception\Definition
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
