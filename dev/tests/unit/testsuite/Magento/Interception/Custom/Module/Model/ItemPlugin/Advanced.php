<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Interception_Custom_Module_Model_ItemPlugin_Advanced
{
    /**
     * @param array $methodArguments
     * @param Magento_Code_Plugin_InvocationChain $invocationChain
     * @return string
     */
    public function aroundGetName(array $methodArguments, Magento_Code_Plugin_InvocationChain $invocationChain)
    {
        return '[' . $invocationChain->proceed($methodArguments) . ']';
    }

    /**
     * @param string $invocationResult
     * @return string
     */
    public function afterGetName($invocationResult)
    {
        return $invocationResult . '%';
    }
}
