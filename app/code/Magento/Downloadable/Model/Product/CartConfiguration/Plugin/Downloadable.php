<?php
/**
 * Plugin for cart product configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Model\Product\CartConfiguration\Plugin;

class Downloadable 
{
    /**
     * Decide whether product has been configured for cart or not
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return bool
     */
    public function aroundIsProductConfigured(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        list($product, $config) = $arguments;

        if ($product->getTypeId() == \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            return isset($config['links']);
        }

        return $invocationChain->proceed($arguments);
    }
} 
