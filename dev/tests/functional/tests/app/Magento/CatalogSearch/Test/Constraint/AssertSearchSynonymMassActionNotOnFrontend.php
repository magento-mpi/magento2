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
 * Class AssertSearchSynonymMassActionNotOnFrontend
 * Assert that you will be not redirected to url from dataset after mass delete search term
 */
class AssertSearchSynonymMassActionNotOnFrontend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert that you will be not redirected to url from dataset after mass delete search term
     *
     * @param array $searchTerms
     * @param CmsIndex $cmsIndex
     * @param Browser $browser
     * @param AssertSearchSynonymNotOnFrontend $assertSearchSynonymNotOnFrontend
     * @return void
     */
    public function processAssert(
        array $searchTerms,
        CmsIndex $cmsIndex,
        Browser $browser,
        AssertSearchSynonymNotOnFrontend $assertSearchSynonymNotOnFrontend
    ) {
        foreach ($searchTerms as $term) {
            $assertSearchSynonymNotOnFrontend->processAssert($cmsIndex, $browser, $term);
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'All search terms were successfully removed (redirect by the synonym was not performed).';
    }
}
