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

namespace Magento\Sales\Test\Block\Backend\Order\View;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Block for items ordered on order page
 *
 * @package Magento\Sales\Test\Block\Backend\Order\View
 */
class Items extends Block
{
    /**
     * Invoice item price xpath selector
     *
     * @var string
     */
    protected $priceSelector = '//div[@class="price-excl-tax"]//span[@class="price"]';

    /**
     * Returns the item price for the specified product.
     *
     * @param Product $product
     * @return array|string
     */
    public function getPrice(Product $product)
    {
        $productName = $product->getProductName();

        $productOptions = array();
        if ($product instanceof ConfigurableProduct) {
            $sku = current($product->getVariationSkus());
            $productOptions = $product->getProductOptions();
        }
        else {
            $sku = $product->getProductSku();
        }
        $productDisplay = $productName . ' SKU: ' . $sku;

        if (!empty($productOptions)) {
            // Working with a configurable product. Make sure we find the correct item on the invoice.
            $productDisplay .= ' ' . key($productOptions) . ' ' . current($productOptions);
        }
        $selector = '//tr[normalize-space(td)="' . $productDisplay .'"]' . $this->priceSelector;

        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->getText();
    }
}
