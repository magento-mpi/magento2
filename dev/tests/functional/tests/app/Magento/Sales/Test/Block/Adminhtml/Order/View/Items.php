<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\View;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;

/**
 * Block for items ordered on order page
 *
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

        if ($product instanceof ConfigurableProduct) {
            // Find the price for the specific configurable product that was purchased
            $productOptions = $product->getProductOptions();
            $checkoutOption = current($productOptions);

            $productDisplay = $productName . ' SKU: ' . $product->getVariationSku($checkoutOption);
            $productDisplay .= ' ' . key($productOptions) . ' ' . $checkoutOption;
        } else {
            $productDisplay = $productName . ' SKU: ' . $product->getProductSku();
        }
        $selector = '//tr[normalize-space(td)="' . $productDisplay .'"]' . $this->priceSelector;

        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->getText();
    }
}
