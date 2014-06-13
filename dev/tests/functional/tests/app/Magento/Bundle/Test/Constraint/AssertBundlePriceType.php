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
use Magento\Bundle\Test\Page\Product\CatalogProductView;

/**
 * Class AssertBundlePriceType
 * Assert that displayed price for bundle items on shopping cart page equals passed from fixture.
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
     * Bundle price block
     *
     * @var string
     */
    protected $bundleBlock = '.fieldset.bundle.options';

    /**
     * Product price type
     *
     * @var string
     */
    protected $productPriceType = 'Dynamic';

    /**
     * Assert that displayed price for bundle items on shopping cart page equals passed from fixture.
     *   Price for bundle items has two options:
     *   1. Fixed (price of bundle product)
     *   2. Dynamic (price of bundle item)
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductBundle $product
     * @param CheckoutCart $checkoutCartView
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductBundle $product,
        CheckoutCart $checkoutCartView
    ) {
        $checkoutCartView->open()->getCartBlock()->clearShoppingCart();
        //Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();

        //Process assertions
        $this->assertPrice($product, $catalogProductView, $checkoutCartView);
    }

    /**
     * Assert prices on the product view Page
     *
     * @param CatalogProductBundle $product
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCartView
     * @return void
     */
    protected function assertPrice(
        CatalogProductBundle $product,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCartView
    ) {
        $catalogProductView->getViewBlock()->clickCustomize();
        $catalogProductView->getViewBlock()->waitForElementVisible($this->bundleBlock);
        $bundleData = $product->getData();
        $this->productPriceType = $product->getPriceType();
        $fillData = $this->convertData($bundleData['bundle_selections']);
        $bundleBlock = $catalogProductView->getBundleViewBlock()->getBundleBlock();
        $bundleBlock->fillBundleOptions($fillData);
        $catalogProductView->getViewBlock()->clickAddToCart();
        $cartBlock = $checkoutCartView->getCartBlock();
        $special_price = 0;
        if(isset($bundleData['group_price'])){
            $special_price = $bundleData['group_price'][array_search('NOT LOGGED IN', $bundleData['group_price'])]
            ['price'];
        }
        $index = 1;
        $sumOptionsPrice = 0;
        foreach ($fillData as $item) {
            $item['price'] -= $special_price;
            \PHPUnit_Framework_Assert::assertEquals(
                "$" . number_format($item['price'], 2),
                $cartBlock->getPriceBundleOptions($index++),
                'Bundle item ' . $index . ' options on frontend is not equals with fixture.'
            );

            $sumOptionsPrice += $item['price'];
        }

        $sumOptionsPrice = (($product->getData()['price_type'] == 'Dynamic')
                ? number_format($sumOptionsPrice, 2)
                : number_format($sumOptionsPrice + $product->getPrice(), 2));
        $subTotal = number_format($cartBlock->getCartItemUnitPrice($product), 2);

        \PHPUnit_Framework_Assert::assertEquals($sumOptionsPrice, $subTotal,
            'Bundle unit prise on frontend is not equals with fixture.'
        );
    }

    /**
     * Convert array for fill on frontend
     *
     * @param array $fields
     * @return array
     */
    protected function convertData(array $fields)
    {
        $newFields = [];
        foreach ($fields['preset'] as $key => $item) {
            $newFields[$key]['type'] = $item['type'];
            $newFields[$key]['required'] = $item['required'];
            if (!isset($fields['products'][0])) {
                break;
            }
            /** @var CatalogProductBundle $fixture */
            $fixture = $fields['products'][0];
            $newFields[$key]['value']['name'] = ($item['type'] == 'Drop-down' || $item['type'] == 'Multiple Select')
                ? $fixture->getName()
                : 'Yes';
            $newFields[$key]['price'] = ($this->productPriceType == 'Fixed')
                ? $item['assigned_products'][0]['data']['selection_price_value']
                : $fixture->getPrice();
        }
        return $newFields;
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Bundle price on shopping cart page is not correct.';
    }
}