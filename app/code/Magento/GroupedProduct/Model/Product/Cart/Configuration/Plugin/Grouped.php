<?php
/**
 * Plugin for cart product configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\Product\Cart\Configuration\Plugin;

class Grouped
{
    /**
     * Decide whether product has been configured for cart or not
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return bool
     */
    public function aroundIsProductConfigured(\Magento\Catalog\Model\Product\CartConfiguration $subject, \Closure $proceed, \Magento\Catalog\Model\Product $product,  $config)
    {
        list($product, $config) = $arguments;

        if ($product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            return isset($config['super_group']);
        }

        return $invocationChain->proceed($arguments);
    }
} 
