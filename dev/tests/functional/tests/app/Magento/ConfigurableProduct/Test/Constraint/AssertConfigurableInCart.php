<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

/**
 * Class AssertProductInCart
 */
class AssertConfigurableInCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert configurable product, corresponds to the product in the cart
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductConfigurable $configurable
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductConfigurable $configurable,
        CheckoutCart $checkoutCart
    ) {
        //Add product to cart
        $catalogProductView->init($configurable);
        $catalogProductView->open();
        $productOptions = $configurable->getConfigurableOptions();
        if ($productOptions) {
            $configurableOption = $catalogProductView->getCustomOptionsBlock();
            $options = $configurableOption->getOptions();
            $key = $productOptions['value']['label']['value'];
            $configurableOption->selectProductCustomOption(reset($options[$key]['value']));
        }
        $catalogProductView->getViewBlock()->clickAddToCart();

        $this->assertOnShoppingCart($configurable, $checkoutCart);
    }

    /**
     * Assert prices on the shopping Cart
     *
     * @param CatalogProductConfigurable $configurable
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    protected function assertOnShoppingCart(CatalogProductConfigurable $configurable, CheckoutCart $checkoutCart)
    {
        /** @var \Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable\Price $priceFixture */
        $priceFixture = $configurable->getDataFieldConfig('price')['fixture'];
        $pricePresetData = $priceFixture->getPreset();

        $price = $checkoutCart->getCartBlock()->getProductPriceByName($configurable->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $pricePresetData['cart_price'],
            $price,
            'Product price in shopping cart is not correct.'
        );
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Product price in shopping cart is not correct.';
    }
}
