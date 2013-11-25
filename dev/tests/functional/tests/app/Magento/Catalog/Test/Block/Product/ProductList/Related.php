<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\ProductList;

use Mtf\TestCase\Functional;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;

class Related extends Block{
    /**
     * Verify that the simple product 2 and configurable product (if any) present as related products
     *
     * @param Product $simpleProduct2
     * @param ConfigurableProduct $configurableProduct
     */
    public function verifyRelatedProducts($simpleProduct2, $configurableProduct=null)
    {
        $rootElement = $this->_rootElement;

        //Verify that simple product 2 is added as related product and has checkbox
        Functional::assertTrue($rootElement->find('[title="'. $simpleProduct2->getProductName() . '"]')->isVisible(),
            'Simple product 2 is not added successfully as related product');
        Functional::assertTrue($rootElement
                ->find('related-checkbox' . $simpleProduct2->getProductId(), Locator::SELECTOR_ID)->isVisible(),
            'Simple product 2 does not have "Add to Cart" checkbox');

        if ($configurableProduct!=null) {
            //Verify that configurable product is added as related product and does not have checkbox
            Functional::assertTrue($rootElement->find('[title="'. $configurableProduct->getProductName() . '"]')->isVisible(),
                'Configurable product is not added successfully as related product');
            Functional::assertFalse($rootElement
                    ->find('related-checkbox' . $configurableProduct->getProductId(), Locator::SELECTOR_ID)->isVisible(),
                'Configurable product should not have "Add to Cart" checkbox');
        }
    }

    /**
     * Add configurable product (with proper option) and related product to the shopping cart
     *
     * @param \Magento\Catalog\Test\Block\Product\View $productViewBlock
     * @param Product $simpleProduct2
     * @param ConfigurableProduct $configurableProduct
     */
    public function addRelatedProductsToCart($productViewBlock, $simpleProduct2, $configurableProduct)
    {
        $productViewBlock->fillOptions($configurableProduct);
        $this->_rootElement
            ->find('related-checkbox' . $simpleProduct2->getProductId(), Locator::SELECTOR_ID)->click();
        $productViewBlock->clickAddToCartButton();
    }
}