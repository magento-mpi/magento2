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
use Magento\Catalog\Test\Block\Product\View;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertProductGroupedPriceOnProductPage
 */
class AssertProductGroupedPriceOnProductPage extends AbstractConstraint implements AssertPriceOnProductPageInterface
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
     * Customer group
     *
     * @var string
     */
    protected $customerGroup;

    /**
     * Assert that displayed grouped price on product page equals passed from fixture
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product, Browser $browser)
    {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        //Process assertions
        $this->assertPrice($product, $catalogProductView->getViewBlock());
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
     * @param View $productViewBlock
     * @param string $customerGroup [optional]
     * @return void
     */
    public function assertPrice(FixtureInterface $product, View $productViewBlock, $customerGroup = 'NOT LOGGED IN')
    {
        $this->customerGroup = $customerGroup;
        $groupPrice = $this->getGroupedPrice($productViewBlock, $product);
        \PHPUnit_Framework_Assert::assertEquals($groupPrice['fixture'], $groupPrice['onPage'], $this->errorMessage);
    }

    /**
     * Get grouped price with fixture product and product page
     *
     * @param View $view
     * @param FixtureInterface $product
     * @return array
     */
    protected function getGroupedPrice(View $view, FixtureInterface $product)
    {
        $fields = $product->getData();
        $groupPrice['onPage'] = $view->getPriceBlock()->getSpecialPrice();
        $groupPrice['fixture'] = number_format(
            $fields['group_price'][array_search($this->customerGroup, $fields['group_price'])]['price'],
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
