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
     * @param \Magento\Catalog\Model\Product\CartConfiguration $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @param array $config
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsProductConfigured(
        \Magento\Catalog\Model\Product\CartConfiguration $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product,
        $config
    ) {
        if ($product->getTypeId() == \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD) {
            return isset($config['giftcard_amount']);
        }

        return $proceed($product, $config);
    }
} 
