<?php
/**
 * Plugin method definitions. Provide the list of interception methods in specified plugin.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception;

interface Definition
{
    /**
     * Retrieve list of methods
     *
     * @param string $type
     * @return string[]
     */
    public function getMethodList($type);
}
