<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Client\Browser;

/**
 * Assert that all configurable attributes is absent on product page on frontend.
 */
class AssertConfigurableAttributesBlockIsAbsentOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that all products attributes is absent on product page on frontend.
     *
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param ConfigurableProductInjectable $product
     * @return void
     */
    public function processAssert(
        Browser $browser,
        CatalogProductView $catalogProductView,
        ConfigurableProductInjectable $product
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        \PHPUnit_Framework_Assert::assertFalse(
            $catalogProductView->getConfigurableAttributesBlock()->isVisible(),
            "Product attributes are present on product page on frontend."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "All product attributes are absent on product page on frontend.";
    }
}
