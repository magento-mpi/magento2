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
 */
class AssertGroupedPriceOnProductPage extends AbstractConstraint implements AssertPriceOnProductPageInterface
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
    protected $errorMessage = 'That displayed grouped price on product page is NOT equal to one, passed from fixture.';

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

        //Process assertions
        $this->assertPrice($product, $catalogProductView);
    }

    /**
     * Set $errorMessage for grouped price assert
     *
     * @param string $errorMessage
     * @return void
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Verify product special price on product view page
     *
     * @param FixtureInterface $product
     * @param CatalogProductView $catalogProductView
     * @param string $block [optional]
     * @return void
     */
    public function assertPrice(
        FixtureInterface $product,
        CatalogProductView $catalogProductView,
        $block = ''
    ) {
        $fields = $product->getData();
        $groupPrice = $catalogProductView->{'get' . $block . 'ViewBlock'}()->getProductPrice();
        $groupPrice = isset($groupPrice['price_special_price'])
            ? $groupPrice['price_special_price']
            : null;

        if (isset($fields['group_price'])) {
            foreach ($fields['group_price'] as $itemGroupPrice) {
                \PHPUnit_Framework_Assert::assertEquals(
                    $itemGroupPrice['price'],
                    $groupPrice,
                    $this->errorMessage
                );
            }
        }
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
