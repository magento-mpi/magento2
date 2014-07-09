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
 * Class AssertProductGroupedPriceOnProductPage
 */
class AssertProductGroupedPriceOnProductPage extends AbstractConstraint
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
        $groupPrice = $this->getGroupedPrice($catalogProductView, $product);
        \PHPUnit_Framework_Assert::assertEquals(
            $groupPrice['fixture'],
            $groupPrice['onPage'],
            'Assert that displayed grouped price on product page NOT equals passed from fixture.'
        );
    }

    /**
     * Get grouped price with fixture product and product page
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param string $customerGroup
     * @return array
     */
    protected function getGroupedPrice(
        CatalogProductView $catalogProductView,
        FixtureInterface $product,
        $customerGroup = 'NOT LOGGED IN'
    ) {
        $fields = $product->getData();
        $groupPrice['onPage'] = $catalogProductView->getViewBlock()->getProductPrice();
        $groupPrice['onPage'] = isset($groupPrice['onPage']['price_special_price'])
            ? $groupPrice['onPage']['price_special_price']
            : null;
        $groupPrice['fixture'] = number_format(
            $fields['group_price'][array_search($customerGroup, $fields['group_price'])]['price'],
            2
        );

        return $groupPrice;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that displayed grouped price on product page equals passed from fixture.';
    }
}
