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
 * Class AssertTermSearchOnFrontend
 */
class AssertTermSearchOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * CMS index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Assert that after save a term search:
     * 1. It displays in the Search field at the top of the page if type set of characters passed from fixture
     * 2. After click 'Go' of Search field opens a results page if it was not specified Redirect URL
     * 3. After click 'Go' of Search field a customer search redirects to a specific page (passed from fixture)
     * if it was specified Redirect URL
     *
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, CatalogSearchQuery $termSearch, Browser $browser)
    {
        $errors = [];
        $this->cmsIndex = $cmsIndex;
        $cmsIndex->open();
        $searchBlock = $cmsIndex->getSearchBlock();

        if ($termSearch->hasData('display_in_terms') && $termSearch->getDisplayInTerms() === 'Yes') {
            $errors = $this->checkSuggestSearch($termSearch);
        }

        $queryText = $termSearch->getQueryText();
        $searchBlock->search($queryText);
        $windowUrl = $browser->getUrl();
        $redirectUrl = $termSearch->getRedirect();
        if ($windowUrl !== $redirectUrl) {
            $errors[] = '- url window (' . $windowUrl . ') does not match the url redirect(' . $redirectUrl . ')';
        }

        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            'When checking on the frontend "Search terms" arose following errors:' . PHP_EOL
            . implode(PHP_EOL, $errors)
        );
    }

    /**
     * Checking is visible suggest block
     *
     * @param CatalogSearchQuery $termSearch
     * @return array
     */
    protected function checkSuggestSearch(CatalogSearchQuery $termSearch)
    {
        $searchBlock = $this->cmsIndex->getSearchBlock();
        if ($termSearch->hasData('num_results')) {
            $isVisible = $searchBlock->isSuggestSearchVisible(
                $termSearch->getQueryText(),
                $termSearch->getNumResults()
            );
        } else {
            $isVisible = $searchBlock->isSuggestSearchVisible($termSearch->getQueryText());
        }

        return $isVisible ? [] : ['- block "Suggest Search" when searching was not found'];
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Checking "Search terms" on frontend successful.';
    }
}
