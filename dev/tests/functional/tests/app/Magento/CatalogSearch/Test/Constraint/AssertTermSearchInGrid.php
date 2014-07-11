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
 * Class AssertTermSearchInGrid
 */
class AssertTermSearchInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after save a term search on edit term search page displays:
     * 1. Correct Search Query field passed from fixture
     * 2. Correct Store
     * 3. Correct Results
     * 4. Correct Uses
     * 5. Correct Synonym
     * 6. Correct Redirect URL
     * 7. Correct Suggested Terms
     *
     * @param CatalogSearchIndex $indexPage
     * @param CatalogSearchQuery $termSearch
     * @return void
     */
    public function processAssert(CatalogSearchIndex $indexPage, CatalogSearchQuery $termSearch)
    {
        $grid = $indexPage->open()->getGrid();
        $filters = [
            'search_query' => $termSearch->getQueryText(),
            'store_id' => $termSearch->getStoreId(),
            'results_from' => $termSearch->getNumResults(),
            'popularity_from' => $termSearch->getPopularity(),
            'synonym_for' => $termSearch->getSynonymFor(),
            'redirect' => $termSearch->getRedirect(),
            'display_in_terms' => strtolower($termSearch->getDisplayInTerms())
        ];

        $grid->search($filters);
        unset($filters['store_id']);
        \PHPUnit_Framework_Assert::assertTrue(
            $grid->isRowVisible($filters),
            'Row terms according to the filters is not found.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Row terms successfully found.';
    }
}
