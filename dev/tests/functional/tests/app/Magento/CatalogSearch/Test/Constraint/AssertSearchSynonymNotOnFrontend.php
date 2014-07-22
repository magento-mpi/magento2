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
 * Class AssertSearchSynonymNotOnFrontend
 * Assert that you will be not redirected to url from dataset
 */
class AssertSearchSynonymNotOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that you will be not redirected to url from dataset
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogSearchQuery $searchTerm
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, Browser $browser, CatalogSearchQuery $searchTerm)
    {
        $queryText = $searchTerm->getQueryText();
        $cmsIndex->open()->getSearchBlock()->search($queryText);
        \PHPUnit_Framework_Assert::assertNotEquals(
            $browser->getUrl(),
            $searchTerm->getRedirect(),
            'Url in the browser corresponds to Url in fixture (redirect has been performed).'
            . PHP_EOL . 'Search term: "' . $queryText . '"'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Search term was successfully removed (redirect by the synonym was not performed).';
    }
}
