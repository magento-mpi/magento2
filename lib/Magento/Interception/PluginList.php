<?php
/**
 * List of plugins configured in application
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Interception_PluginList
{
    /**
     * Retrieve list of plugins listening for method
     *
     * @param string $type
     * @param string $method
     * @param string $scenario
     * @return array
     */
    public function getPlugins($type, $method, $scenario);
}
