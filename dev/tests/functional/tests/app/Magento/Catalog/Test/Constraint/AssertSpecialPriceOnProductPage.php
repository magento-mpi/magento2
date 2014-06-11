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
 * Class AssertSpecialPriceOnProductPage
 */
class AssertSpecialPriceOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Error message
     *
     * @var string
     */
    public $errMessage = 'Assert that displayed special price on product page NOT equals passed from fixture.';

    /**
     * Assert that displayed special price on product page equals passed from fixture
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        $catalogProductView->init($product);
        $catalogProductView->open();

        //Process assertions
        $this->assertSpecialPrice($product, $catalogProductView);
    }

    /**
     * Verify product special price on product view page
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param string $block
     * @return void
     */
    public function assertSpecialPrice(
        FixtureInterface $product,
        CatalogProductView $catalogProductView,
        $block = 'View'
    ) {
        $fields = $product->getData();
        $specialPrice = $catalogProductView->{'get' . $block . 'Block'}()->getProductPrice();
        $specialPrice = (isset($specialPrice['price_special_price']))
            ? $specialPrice['price_special_price']
            : null;
        if (isset($fields['special_price'])) {
            \PHPUnit_Framework_Assert::assertEquals(
                number_format($fields['special_price'], 2),
                $specialPrice,
                $this->errMessage
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Assert that displayed special price on product page equals passed from fixture.";
    }
}
