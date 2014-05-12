<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertProductInCart
 */
class AssertBundleInCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Checking bundle product in cart
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductBundle $bundle
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductBundle $bundle,
        CheckoutCart $checkoutCart
    ) {
        //Add product to cart
        $catalogProductView->init($bundle);
        $catalogProductView->open();

        $catalogProductView->getViewBlock()->clickCustomize();

        $optionsBlock = $catalogProductView->getCustomOptionsBlock();
        /** @var \Magento\Bundle\Test\Fixture\Bundle\Selections $selectionsFixture */
        $selectionsFixture = $bundle->getDataFieldConfig('bundle_selections')['source'];
        $bundleOptions = $selectionsFixture->getSelectionForCheckout();
        if ($bundleOptions) {
            $catalogProductView->getViewBlock()->getBundleBlock()->fillBundleOptions($bundleOptions);
        }
        $productOptions = $bundle->getCustomOptions();
        if ($productOptions) {
            $options = $optionsBlock->getBundleCustomOptions();
            $key = $productOptions[0]['title'];
            $optionsBlock->selectProductCustomOption($options[$key][1]);
        }
        $catalogProductView->getViewBlock()->clickAddToCart();

        $this->assertOnShoppingCart($bundle, $checkoutCart);
    }

    /**
     * Assert prices on the shopping Cart
     *
     * @param CatalogProductBundle $bundle
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    protected function assertOnShoppingCart(CatalogProductBundle $bundle, CheckoutCart $checkoutCart)
    {
        /** @var \Magento\Catalog\Test\Fixture\CatalogProductSimple\Price $priceFixture */
        $priceFixture = $bundle->getDataFieldConfig('price')['source'];
        $pricePresetData = $priceFixture->getPreset();

        $price = $checkoutCart->getCartBlock()->getProductPriceByName($bundle->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $pricePresetData['cart_price'],
            $price,
            'Bundle price in shopping cart is not correct.'
        );
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle price in shopping cart is not correct.';
    }
}
