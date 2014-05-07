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
 *
 * @package Magento\Catalog\Test\Constraint
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
        $fields = $product->getData();
        $specialPrice = $catalogProductView->getViewBlock()->getProductPrice();
        $specialPrice = isset($specialPrice['price_special_price']) ? $specialPrice['price_special_price'] : null;
        if (!empty($fields['special_price'])) {
            \PHPUnit_Framework_Assert::assertEquals(
                $fields['special_price'],
                $specialPrice,
                'Assert that displayed special price on product page NOT equals passed from fixture.'
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Assert that displayed special price on product page equals passed from fixture";
    }
}
