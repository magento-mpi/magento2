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
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProduct;

/**
 * Class Items
 * Block for items ordered on order page
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
        $productName = $product->getName();

        if ($product instanceof ConfigurableProduct) {
            // Find the price for the specific configurable product that was purchased
            $configurableAttributes = $product->getConfigurableAttributes();
            $productOptions = $product->getCheckoutData()['options']['configurable_options'];
            $checkoutOption = reset($productOptions);
            $attributeKey = $checkoutOption['title'];
            $optionKey = $checkoutOption['value'];
            $attributeValue = $configurableAttributes[$attributeKey]['label']['value'];
            $optionValue = $configurableAttributes[$attributeKey][$optionKey]['option_label']['value'];

            $productDisplay = $productName . ' SKU: ' . $product->getVariationSku($checkoutOption);
            $productDisplay .= ' ' . $attributeValue . ' ' . $optionValue;
        } else {
            $productDisplay = $productName . ' SKU: ' . $product->getSku();
        }
        $selector = '//tr[normalize-space(td)="' . $productDisplay .'"]' . $this->priceSelector;

        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->getText();
    }
}
