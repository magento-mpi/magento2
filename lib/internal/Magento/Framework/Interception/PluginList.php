<?php
/**
 * List of plugins configured in application
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Interception;

interface PluginList
{
    /**
     * Retrieve next plugins in chain
     *
     * @param string $type
     * @param string $method
     * @param string $code
     * @return array
     */
    public function getNext($type, $method, $code = null);

    /**
     * Retrieve plugin instance by code
     *
     * @param string $type
     * @param string $code
     * @return mixed
     */
    public function getPlugin($type, $code);
}
