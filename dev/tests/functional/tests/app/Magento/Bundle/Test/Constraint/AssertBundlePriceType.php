<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertBundlePriceType
 */
class AssertBundlePriceType extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Product price type
     *
     * @var string
     */
    protected $productPriceType = 'Dynamic';

    /**
     * Assert that displayed price for bundle items on shopping cart page equals to passed from fixture.
     *   Price for bundle items has two options:
     *   1. Fixed (price of bundle product)
     *   2. Dynamic (price of bundle item)
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductBundle $product
     * @param CheckoutCart $checkoutCartView
     * @param Browser $browser
     * @param CatalogProductBundle $originalProduct [optional]
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductBundle $product,
        CheckoutCart $checkoutCartView,
        Browser $browser,
        CatalogProductBundle $originalProduct = null
    ) {
        $checkoutCartView->open()->getCartBlock()->clearShoppingCart();
        //Open product view page
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        //Process assertions
        $this->assertPrice($product, $catalogProductView, $checkoutCartView, $originalProduct);
    }

    /**
     * Assert prices on the product view page and shopping cart page.
     *
     * @param CatalogProductBundle $product
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCartView
     * @param CatalogProductBundle $originalProduct [optional]
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function assertPrice(
        CatalogProductBundle $product,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCartView,
        CatalogProductBundle $originalProduct = null
    ) {
        $customerGroup = 'NOT LOGGED IN';
        $catalogProductView->getViewBlock()->clickCustomize();
        $bundleData = $product->getData();
        $this->productPriceType = $originalProduct !== null
            ? $originalProduct->getPriceType()
            : $product->getPriceType();
        $fillData = $product->getDataFieldConfig('checkout_data')['source']->getPreset();
        $bundleBlock = $catalogProductView->getViewBlock()->getBundleBlock();
        $bundleBlock->addToCart($product, $catalogProductView);
        $cartItem = $checkoutCartView->getCartBlock()->getCartItem($product);
        $specialPrice = 0;
        if (isset($bundleData['group_price'])) {
            $specialPrice =
                $bundleData['group_price'][array_search($customerGroup, $bundleData['group_price'])]['price'] / 100;
        }

        $optionPrice = [];
        foreach ($fillData['bundle_options'] as $key => $data) {
            $subProductPrice = 0;
            foreach ($bundleData['bundle_selections']['products'][$key] as $productKey => $itemProduct) {
                if (strpos($itemProduct->getName(), $data['value']['name']) !== false) {
                    $data['value']['key'] = $productKey;
                    $subProductPrice = $itemProduct->getPrice();
                }
            }

            $optionPrice[$key]['price'] = $this->productPriceType == 'Fixed'
                ? number_format(
                    $bundleData['bundle_selections']['bundle_options'][$key]['assigned_products'][$data['value']['key']]
                    ['data']['selection_price_value'],
                    2
                )
                : number_format($subProductPrice, 2);
        }

        foreach ($optionPrice as $index => $item) {
            $item['price'] -= $item['price'] * $specialPrice;
            \PHPUnit_Framework_Assert::assertEquals(
                number_format($item['price'], 2),
                $cartItem->getPriceBundleOptions($index + 1),
                'Bundle item ' . ($index + 1) . ' options on frontend don\'t equal to fixture.'
            );
        }
        $sumOptionsPrice = $product->getDataFieldConfig('price')['source']->getPreset()['cart_price'];

        $subTotal = number_format($cartItem->getPrice(), 2);
        \PHPUnit_Framework_Assert::assertEquals(
            $sumOptionsPrice,
            $subTotal,
            'Bundle unit price on frontend doesn\'t equal to fixture.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle price on shopping cart page is not correct.';
    }
}
