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
 * Class AssertGroupedPriceOnProductPage
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertGroupedPriceOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed grouped price on product page equals passed from fixture
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
        $groupPrice = $catalogProductView->getViewBlock()->getProductPrice();
        $groupPrice = empty($groupPrice['price_special_price']) ? null : $groupPrice['price_special_price'];
        if (!empty($fields['group_price'])) {
            foreach ($fields['group_price'] as $itemGroupPrice) {
                \PHPUnit_Framework_Assert::assertEquals(
                    $itemGroupPrice['price'],
                    $groupPrice,
                    'Assert that displayed grouped price on product page NOT equals passed from fixture.'
                );
            }
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that displayed grouped price on product page equals passed from fixture.';
    }
}
