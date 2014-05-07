<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
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
     * Assertion that the product page is displayed correctly
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        FixtureInterface $product
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
     * @param FixtureInterface $product
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    protected function assertOnProductView(FixtureInterface $product, CatalogProductView $catalogProductView)
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
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product on product view page is not correct.';
    }
}
