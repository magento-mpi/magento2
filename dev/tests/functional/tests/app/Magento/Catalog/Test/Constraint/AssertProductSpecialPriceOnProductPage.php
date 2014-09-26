<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertProductSpecialPriceOnProductPage
 */
class AssertProductSpecialPriceOnProductPage extends AbstractConstraint implements AssertPriceOnProductPageInterface
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
    protected $errorMessage = 'Assert that displayed special price on product page NOT equals to passed from fixture.';

    /**
     * Assert that displayed special price on product page equals passed from fixture
     *
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, Browser $browser, FixtureInterface $product)
    {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        //Process assertions
        $this->assertPrice($product, $catalogProductView);
    }

    /**
     * Set $errorMessage for special price assert
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
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param string $block [optional]
     * @return void
     */
    public function assertPrice(
        FixtureInterface $product,
        CatalogProductView $catalogProductView,
        $block = ''
    ) {
        $fields = $product->getData();
        $specialPrice = $catalogProductView->getViewBlock()->getPriceBlock()->getSpecialPrice();
        if (isset($fields['special_price'])) {
            \PHPUnit_Framework_Assert::assertEquals(
                number_format($fields['special_price'], 2),
                $specialPrice,
                $this->errorMessage
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
