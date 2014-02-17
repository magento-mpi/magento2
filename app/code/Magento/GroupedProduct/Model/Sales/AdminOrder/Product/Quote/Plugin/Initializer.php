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
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Sales\Model\Quote\Item|string
     */
    public function aroundInit(\Magento\Sales\Model\AdminOrder\Product\Quote\Initializer $subject, \Closure $proceed, \Magento\Sales\Model\Quote $quote, \Magento\Catalog\Model\Product $product, \Magento\Object $config)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $arguments[0];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $arguments[1];
        /** @var \Magento\Object $config */
        $config = $arguments[2];

        $item = $invocationChain->proceed($arguments);

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
