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
use Magento\CatalogSearch\Test\Page\Adminhtml\CatalogSearchEdit;
use Magento\CatalogSearch\Test\Page\Adminhtml\CatalogSearchIndex;

/**
 * Class AssertTermSearchForm
 */
class AssertTermSearchForm extends AbstractConstraint
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
     * 3. Correct Number of results
     * 4. Correct Number of Uses
     * 5. Correct Synonym For
     * 6. Correct Redirect URL
     * 7. Correct Display in Suggested Terms
     *
     * @param CatalogSearchIndex $indexPage
     * @param CatalogSearchEdit $editPage
     * @param CatalogSearchQuery $termSearch
     */
    public function processAssert(
        CatalogSearchIndex $indexPage,
        CatalogSearchEdit $editPage,
        CatalogSearchQuery $termSearch
    ) {
        $indexPage->open()->getGrid()->searchAndOpen(['search_query' => $termSearch->getQueryText()]);
        $formData = $editPage->getForm()->getData($termSearch);
        $fixtureData = $termSearch->getData();

        \PHPUnit_Framework_Assert::assertEquals(
            array_map('trim', $formData),
            array_map('trim', $fixtureData),
            'This form "Search Terms" does not match the fixture data.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'These form "Search Terms" correspond to the fixture data.';
    }
}
