<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Backend\Order;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Block for items ordered on order page
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class ItemsOrdered extends Block
{
    /**
     * @param \Magento\Catalog\Test\Fixture\Product $product
     * @return array|string
     */
    public function getPrice(\Magento\Catalog\Test\Fixture\Product $product)
    {
        $productName = $product->getProductName();
        $sku = $product->getProductSku();

        $productOptions = array();
        if ($product instanceof \Magento\Catalog\Test\Fixture\ConfigurableProduct) {
            $sku = current($product->getVariationSkus());
            $productOptions = $product->getProductOptions();
        }
        $productDisplay = $productName . ' SKU: ' . $sku;

        if (!empty($productOptions)) {
            // Working with a configurable product. Make sure we find the correct item on the invoice.
            $productDisplay .= ' ' . key($productOptions) . ' ' . current($productOptions);
        }
        $selector = '//tr[normalize-space(td)="'. $productDisplay .'"]'
            . '//div[@class="price-excl-tax"]//span[@class="price"]';
        $price = $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->getText();

        return $price;
    }
}
