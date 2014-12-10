<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Mtf\Client\Browser;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;

/**
 * Class AssertSearchTermMassActionNotOnFrontend
 * Assert that after mass delete a search term not redirect to url in dataset
 */
class AssertSearchTermMassActionNotOnFrontend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert that after mass delete a search term not redirect to url in dataset
     *
     * @param array $searchTerms
     * @param CmsIndex $cmsIndex
     * @param Browser $browser
     * @param AssertSearchTermNotOnFrontend $assertSearchTermNotOnFrontend
     * @return void
     */
    public function processAssert(
        array $searchTerms,
        CmsIndex $cmsIndex,
        Browser $browser,
        AssertSearchTermNotOnFrontend $assertSearchTermNotOnFrontend
    ) {
        foreach ($searchTerms as $term) {
            /** @var CatalogSearchQuery $term */
            $assertSearchTermNotOnFrontend->processAssert($cmsIndex, $browser, $term);
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'All search terms were successfully removed (redirects to the specified URL was not performed).';
    }
}
