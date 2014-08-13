<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertProductView
 *
 * @package Magento\Catalog\Test\Constraint
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
     * @return void
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
     * Assert data on the product view page
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    protected function assertOnProductView(CatalogProductSimple $product, CatalogProductView $catalogProductView)
    {
        $viewBlock = $catalogProductView->getViewBlock();
        $price = $viewBlock->getPriceBlock()->getPrice();
        $name = $viewBlock->getProductName();
        $sku = $viewBlock->getProductSku();

        \PHPUnit_Framework_Assert::assertEquals(
            $product->getName(),
            $name,
            'Product name on product view page is not correct.'
        );
        \PHPUnit_Framework_Assert::assertEquals(
            $product->getSku(),
            $sku,
            'Product sku on product view page is not correct.'
        );

        if (isset($price['price_regular_price'])) {
            \PHPUnit_Framework_Assert::assertEquals(
                number_format($product->getPrice(), 2),
                $price['price_regular_price'],
                'Product regular price on product view page is not correct.'
            );
        }

        $priceComparing = false;
        if ($specialPrice = $product->getSpecialPrice()) {
            $priceComparing = $specialPrice;
        }

        if ($groupPrice = $product->getGroupPrice()) {
            $groupPrice = reset($groupPrice);
            $priceComparing = $groupPrice['price'];
        }

        if ($priceComparing && isset($price['price_special_price'])) {
            \PHPUnit_Framework_Assert::assertEquals(
                number_format($priceComparing, 2),
                $price['price_special_price'],
                'Product special price on product view page is not correct.'
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
        return 'Product data on product view page is not correct.';
    }
}
