<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

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

        if ($catalogSearch->hasData('num_results')) {
            $isVisible = $searchBlock->isSuggestSearchVisible($queryText, $catalogSearch->getNumResults());
        } else {
            $isVisible = $searchBlock->isSuggestSearchVisible($queryText);
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isVisible,
            'Block "Suggest Search" when searching was not found'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Asserts window contains requested entity and quantity';
    }
}
