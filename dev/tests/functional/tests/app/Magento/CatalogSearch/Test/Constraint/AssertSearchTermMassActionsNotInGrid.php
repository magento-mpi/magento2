<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Magento\CatalogSearch\Test\Page\Adminhtml\CatalogSearchIndex;

/**
 * Class AssertSearchTermMassActionsNotInGrid
 * Assert that after mass delete a search term on grid page not displayed
 */
class AssertSearchTermMassActionsNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after mass delete a search term on grid page not displayed
     *
     * @param array $searchTerms
     * @param CatalogSearchIndex $indexPage
     * @param AssertSearchTermNotInGrid $assertSearchTermNotInGrid
     * @return void
     */
    public function processAssert(
        array $searchTerms,
        CatalogSearchIndex $indexPage,
        AssertSearchTermNotInGrid $assertSearchTermNotInGrid
    ) {
        foreach ($searchTerms as $term) {
            /** @var CatalogSearchQuery $term */
            $assertSearchTermNotInGrid->processAssert($indexPage, $term);
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Search term was not found in grid.';
    }
}
