<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class SimpleClassPluginA
{
    /**
     * @param array $arguments
     * @param Magento_Code_Plugin_InvocationChain $invocationChain
     * @return string
     */
    public function aroundDoWork(array $arguments, Magento_Code_Plugin_InvocationChain $invocationChain)
    {
        return '<PluginA>' . $invocationChain->proceed($arguments) . '</PluginA>';
    }
}

