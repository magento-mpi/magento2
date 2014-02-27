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
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundHasOptions(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        return $product->getIsRecurring() ?: $proceed($product);
    }
}
