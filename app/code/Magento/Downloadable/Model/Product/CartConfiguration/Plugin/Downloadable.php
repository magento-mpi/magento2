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
        if ($product->getTypeId() == \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            return isset($config['links']);
        }
        return $proceed($product, $config);
    }
} 
