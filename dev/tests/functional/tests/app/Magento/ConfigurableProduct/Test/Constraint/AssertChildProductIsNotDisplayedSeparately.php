<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\CatalogSearch\Test\Page\CatalogsearchResult;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

/**
 * Class AssertChildProductIsNotDisplayedSeparately
 */
class AssertChildProductIsNotDisplayedSeparately extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that products generated during configurable product creation - are not visible on frontend(by default).
     *
     * @param CatalogsearchResult $catalogSearchResult
     * @param CmsIndex $cmsIndex
     * @param ConfigurableProductInjectable $product
     * @return void
     */
    public function processAssert(
        CatalogsearchResult $catalogSearchResult,
        CmsIndex $cmsIndex,
        ConfigurableProductInjectable $product
    ) {
        $configurableAttributesData = $product->getConfigurableAttributesData();
        $errors = [];

        $cmsIndex->open();
        foreach ($configurableAttributesData['matrix'] as $variation) {
            $cmsIndex->getSearchBlock()->search($variation['sku']);

            if ($catalogSearchResult->getListProductBlock()->isProductVisible($product->getName())) {
                $errors[] = 'Child product with sku: "' . $variation['sku'] . '" is visible on frontend(by default).';
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty($errors, implode($errors, ' '));
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Child products generated during configurable product creation are not visible on frontend(by default)';
    }
}

