<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Plugin;

/**
 * Class PriceBackend
 *
 *  Make price validation optional for bundle dynamic
 */
class PriceBackend
{
    /**
     * @param \Magento\Catalog\Model\Product\Attribute\Backend\Price $subject
     * @param Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function aroundValidate(
        \Magento\Catalog\Model\Product\Attribute\Backend\Price $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        if ($product->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC) {
            return true;
        } else {
            return $proceed($product);
        }
    }
} 
