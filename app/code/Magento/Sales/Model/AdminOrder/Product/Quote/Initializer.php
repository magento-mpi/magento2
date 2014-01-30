<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product quote initializer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 */
namespace Magento\Sales\Model\AdminOrder\Product\Quote;

class Initializer
{
    /**
     * @param \Magento\Sales\Model\Quote $quote
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Object $config
     * @return \Magento\Sales\Model\Quote\Item|string
     */
    public function init(
        \Magento\Sales\Model\Quote $quote,
        \Magento\Catalog\Model\Product $product,
        \Magento\Object $config
    ) {
        $stockItem = $product->getStockItem();
        if ($stockItem && $stockItem->getIsQtyDecimal()) {
            $product->setIsQtyDecimal(1);
        } else {
            $config->setQty((int) $config->getQty());
        }

        $product->setCartQty($config->getQty());

        $item = $quote->addProductAdvanced(
            $product,
            $config,
            \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
        );

        return $item;
    }

}
