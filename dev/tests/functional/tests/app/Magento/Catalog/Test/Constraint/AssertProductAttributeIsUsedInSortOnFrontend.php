<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\CatalogSearch\Test\Page\CatalogsearchResult;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Assert that attribute present in sort dropdown on search results page on frontend.
 */
class AssertProductAttributeIsUsedInSortOnFrontend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that attribute present in sort dropdown on search results page on frontend.
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogsearchResult $catalogsearchResult
     * @param CatalogProductAttribute $attribute
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogsearchResult $catalogsearchResult,
        CatalogProductAttribute $attribute,
        InjectableFixture $product
    ) {
        $cmsIndex->open()->getSearchBlock()->search($product->getName());
        $label = $attribute->hasData('manage_frontend_label')
            ? $attribute->getManageFrontendLabel()
            : $attribute->getFrontendLabel();

        \PHPUnit_Framework_Assert::assertTrue(
            in_array($label, $catalogsearchResult->getListProductBlock()->getSortByValues()),
            'Attribute is absent in sort dropdown on search results page on frontend.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is present in sort dropdown on search results page on frontend.';
    }
}
