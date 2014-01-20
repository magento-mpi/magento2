<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Block\Adminhtml\Order\Create;

class Sidebar 
{
    /**
     * Get item qty
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed|string
     */
    public function aroundGetItemQty(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item */
        $item = $arguments[0];
        if ($item->getProduct()->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            return '';
        }
        return $invocationChain->proceed($arguments);
    }

    /**
     * Check whether product configuration is required before adding to order
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return bool|mixed
     */
    public function aroundIsConfigurationRequired(
        array $arguments,
        \Magento\Code\Plugin\InvocationChain $invocationChain
    ) {
        $typeId = $arguments[0];
        if ($typeId == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            return true;
        }
        return $invocationChain->proceed($arguments);
    }
} 
