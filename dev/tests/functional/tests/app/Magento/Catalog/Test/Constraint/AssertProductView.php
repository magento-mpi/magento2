<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductView
 */
class AssertProductView extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductSimple $product
    ) {
        //Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();

        //Process assertions
        $this->assertOnProductView($product, $catalogProductView);
    }

    /**
     * Assert prices on the product view Page
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     */
    protected function assertOnProductView(CatalogProductSimple $product, CatalogProductView $catalogProductView)
    {
        /** @var \Magento\Catalog\Test\Fixture\CatalogProductSimple\Price $priceFixture */
        $priceFixture = $product->getDataFieldConfig('price')['source'];
        $pricePresetData = $priceFixture->getPreset();

        if (isset($pricePresetData['product_special_price'])) {
            $regularPrice = $catalogProductView->getViewBlock()->getProductPriceBlock()->getRegularPrice();
            \PHPUnit_Framework_Assert::assertEquals(
                $pricePresetData['product_price'],
                $regularPrice,
                'Product regular price on product view page is not correct.'
            );

            $specialPrice = $catalogProductView->getViewBlock()->getProductPriceBlock()->getSpecialPrice();
            \PHPUnit_Framework_Assert::assertEquals(
                $pricePresetData['product_special_price'],
                $specialPrice,
                'Product special price on product view page is not correct.'
            );
        } else {
            $price = $catalogProductView->getViewBlock()->getProductPriceBlock()->getPrice();
            \PHPUnit_Framework_Assert::assertContains(
                (string)$price,
                $pricePresetData['product_price'],
                'Product price on product view page is not correct.'
            );
        }
    }

    /**
     * Text of Visible in category assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Product price on product view page is not correct.';
    }
}
