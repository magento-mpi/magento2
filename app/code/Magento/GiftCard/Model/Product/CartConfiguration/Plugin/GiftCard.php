<?php
/**
 * Plugin for cart product configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Model\Product\CartConfiguration\Plugin;

class GiftCard
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
        /** @var $product \Magento\Catalog\Model\Product */
        list($product, $config) = $arguments;

        if ($product->getTypeId() == \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD) {
            return isset($config['giftcard_amount']);
        }

        return $invocationChain->proceed($arguments);
    }
} 
