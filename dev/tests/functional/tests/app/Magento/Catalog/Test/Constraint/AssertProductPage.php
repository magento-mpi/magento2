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
 * Class AssertProductPage
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Process assert
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductSimple $product
    ) {
        // TODO fix initialization url for frontend page
        //Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();

        //Process assertions
        $this->assertOnProductView($product, $catalogProductView);
    }

    /**
     * Assert prices on the product view page
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    protected function assertOnProductView(CatalogProductSimple $product, CatalogProductView $catalogProductView)
    {
        $viewBlock = $catalogProductView->getViewBlock();
        $price = $viewBlock->getProductPriceBlock()->getPrice();
        $name = $viewBlock->getProductName();
        $sku = $viewBlock->getProductSku();
        $shortDescription = $viewBlock->getProductShortDescription();
        $description = $viewBlock->getProductDescription();

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
        \PHPUnit_Framework_Assert::assertEquals(
            number_format($product->getPrice(), 2),
            $price['price_regular_price'],
            'Product regular price on product view page is not correct.'
        );
        \PHPUnit_Framework_Assert::assertEquals(
            $product->getShortDescription(),
            $shortDescription,
            'Product short description on product view page is not correct.'
        );
        \PHPUnit_Framework_Assert::assertEquals(
            $product->getDescription(),
            $description,
            'Product description on product view page is not correct.'
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Product on product view page is not correct.';
    }
}
