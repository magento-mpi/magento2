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
class Magento_Code_Plugin_GeneratorTest_SimpleClassPluginA
{
    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return string
     */
    public function aroundDoWork(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        return '<PluginA>' . $invocationChain->proceed($arguments) . '</PluginA>';
    }
}

