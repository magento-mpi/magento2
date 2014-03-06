<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product quote initializer plugin
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 */
namespace Magento\GroupedProduct\Model\Sales\AdminOrder\Product\Quote\Plugin;


use Magento\GroupedProduct\Model\Product\Type\Grouped;

class Initializer
{
    /**
     * @param \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer $subject
     * @param callable $proceed
     * @param \Magento\Sales\Model\Quote $quote
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Object $config
     *
     * @return \Magento\Sales\Model\Quote\Item|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundInit(
        \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Quote $quote,
        \Magento\Catalog\Model\Product $product,
        \Magento\Object $config
    ) {
        $item = $proceed($quote, $product, $config);

        if (is_string($item) && $product->getTypeId() != Grouped::TYPE_CODE) {
            $item = $quote->addProductAdvanced(
                $product,
                $config,
                \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_LITE
            );
        }
        return $item;
    }
}
