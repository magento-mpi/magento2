<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GroupedProduct\Model\Product\Initialization\Helper\ProductLinks\Plugin;

class Grouped
{
    /**
     * Initialize grouped product links
     *
     * @param \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param array $links
     *
     * @return \Magento\Catalog\Model\Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterInitializeLinks(
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $subject,
        \Magento\Catalog\Model\Product $product,
        array $links
    ) {
        if (isset($links['grouped']) && !$product->getGroupedReadonly()) {
            $product->setGroupedLinkData((array)$links['grouped']);
        }

        return $product;
    }
}
