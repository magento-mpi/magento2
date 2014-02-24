<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model\ProductType;

class Plugin
{
    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return array|mixed
     */
    public function aroundHasOptions(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $product = $arguments[0];
        if ($product->getIsRecurring()) {
            return true;
        }
        return $invocationChain->proceed($arguments);
    }

}
