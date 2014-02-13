<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model\Plugin;

class ProductType
{
    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return array|mixed
     */
    public function aroundHasOptions(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $product = $arguments['product'];
        if ($product->getIsRecurring() == '1') {
            return true;
        }
        return $invocationChain->proceed($arguments);
    }

}
