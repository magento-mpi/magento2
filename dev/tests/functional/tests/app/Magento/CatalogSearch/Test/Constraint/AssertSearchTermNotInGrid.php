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
 * Class AssertSearchTermNotInGrid
 * Assert that after delete a search term on grid page not displayed
 */
class AssertSearchTermNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after delete a search term on grid page not displayed
     *
     * @param CatalogSearchIndex $indexPage
     * @param CatalogSearchQuery $searchTerm
     * @return void
     */
    public function processAssert(CatalogSearchIndex $indexPage, CatalogSearchQuery $searchTerm)
    {
        $grid = $indexPage->open()->getGrid();
        $filters = [
            'search_query' => $searchTerm->getQueryText(),
            'store_id' => $searchTerm->getStoreId(),
            'results_from' => $searchTerm->getNumResults(),
            'popularity_from' => $searchTerm->getPopularity(),
            'synonym_for' => $searchTerm->getSynonymFor(),
            'redirect' => $searchTerm->getRedirect(),
            'display_in_terms' => strtolower($searchTerm->getDisplayInTerms())
        ];

        unset($filters['store_id']);
        \PHPUnit_Framework_Assert::assertFalse(
            $grid->isRowVisible($filters),
            'Search term was found on the grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Search term is not display on the grid.';
    }
}
