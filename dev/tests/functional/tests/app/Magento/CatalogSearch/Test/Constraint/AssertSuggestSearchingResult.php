<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;

/**
 * Class AssertSuggestSearchingResult
 */
class AssertSuggestSearchingResult extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Check that after input some text(e.g. product name) into search field, drop-down window is appeared.
     * Window contains requested entity and number of quantity.
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogSearchQuery $catalogSearch
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, CatalogSearchQuery $catalogSearch)
    {
        $cmsIndex->open();
        $searchBlock = $cmsIndex->getSearchBlock();

        $queryText = $catalogSearch->getQueryText();
        $searchBlock->fillSearch($queryText);

        if ($amount = $catalogSearch->getNumResults()) {
            $isVisible = $searchBlock->isSuggestSearchVisible($queryText, $amount);
        } else {
            $isVisible = $searchBlock->isSuggestSearchVisible($queryText);
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isVisible,
            'Block "Suggest Search" when searching was not found'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Asserts window contains requested entity and quantity';
    }
}
