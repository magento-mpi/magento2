<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Custom\Module\Model\ItemPlugin;

class Advanced
{
    /**
     * @param array $methodArguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return string
     */
    public function aroundGetName(array $methodArguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
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
