<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Check whether html tags are using in an attribute value.
 */
class AssertProductAttributeIsHtmlAllowed extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Check whether html tags are using in attribute value.
     * Checked tag structure <b><i>atttribute_default_value</i></b>
     *
     * @param InjectableFixture $product
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @throws \Exception
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CatalogProductAttribute $attribute,
        CatalogProductView $catalogProductView,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        \PHPUnit_Framework_Assert::assertTrue(
            $catalogProductView->getAdditionalInformationBlock()->hasHtmlTagInAttributeValue($attribute),
            'Attribute is not visible with HTML tags on frontend.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is visible with HTML tags on frontend.';
    }
}
