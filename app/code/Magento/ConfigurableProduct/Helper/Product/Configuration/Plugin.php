<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Helper\Product\Configuration;

use Magento\Code\Plugin\InvocationChain;

class Plugin
{
    /**
     * Retrieve configuration options for configurable product
     *
     * @param array $arguments
     * @param InvocationChain $invocationChain
     * @return array
     */
    public function aroundGetOptions(array $arguments, InvocationChain $invocationChain)
    {
        /** @var \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item */
        $item = $arguments[0];
        $product = $item->getProduct();
        $typeId = $product->getTypeId();
        if ($typeId == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $attributes = $product->getTypeInstance()->getSelectedAttributesInfo($product);
            return array_merge($attributes, $invocationChain->proceed($arguments));
        }
        return $invocationChain->proceed($arguments);
    }
} 
