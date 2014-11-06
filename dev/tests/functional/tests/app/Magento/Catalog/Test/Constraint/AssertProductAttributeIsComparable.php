<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Page\Product\CatalogProductCompare;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Check whether there is an opportunity to compare products using given attribute.
 */
class AssertProductAttributeIsComparable extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Check whether there is an opportunity to compare products using given attribute.
     *
     * @param InjectableFixture $product
     * @param CatalogProductAttribute $attribute
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductCompare $catalogProductCompare
     */
    public function processAssert(
        InjectableFixture $product,
        CatalogProductAttribute $attribute,
        Browser $browser,
        CatalogProductView $catalogProductView,
        CatalogProductCompare $catalogProductCompare
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCompare();
        $catalogProductCompare->open();
        $label = $attribute->hasData('manage_frontend_label')
            ? $attribute->getManageFrontendLabel()
            : $attribute->getFrontendLabel();

        \PHPUnit_Framework_Assert::assertTrue(
            $catalogProductCompare->getCompareProductsBlock()->getCompareProductAttribute($label)->isVisible(),
            'Attribute is absent on product compare page.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is present on product compare page.';
    }
}
