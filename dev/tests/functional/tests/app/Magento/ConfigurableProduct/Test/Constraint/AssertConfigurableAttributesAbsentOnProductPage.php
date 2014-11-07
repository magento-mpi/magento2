<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Client\Browser;

/**
 * Assert that deleted products attributes are absent on product page on frontend.
 */
class AssertConfigurableAttributesAbsentOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted products attributes are absent on product page on frontend.
     *
     * @param CatalogProductAttribute[] $deletedProductAttributes
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param ConfigurableProductInjectable $product
     * @return void
     */
    public function processAssert(
        array $deletedProductAttributes,
        Browser $browser,
        CatalogProductView $catalogProductView,
        ConfigurableProductInjectable $product
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $pageOptions = $catalogProductView->getViewBlock()->getOptions($product)['configurable_options'];

        foreach ($deletedProductAttributes as $attribute) {
            $attributeLabel = $attribute->getFrontendLabel();
            \PHPUnit_Framework_Assert::assertFalse(
                isset($pageOptions[$attributeLabel]),
                "Product attribute '$attributeLabel' found on product page on frontend."
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
        return "Product attributes are absent on product page on frontend.";
    }
}
