<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Search\Test\Fixture\CatalogSearchQuery;

/**
 * Cover Suggest Searching Result (SearchEntity) with functional tests designed for automation
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Two "default" test simple products is created
 * 2. Navigate to frontend
 * 3. Input in "Search" field(top-right part of the index page, near cart icon) 'Simple' and press "Enter" key
 *
 * Steps:
 * 1. Go to frontend on index page
 * 2. Input in "Search" field test data
 * 3. Perform asserts
 *
 * @group Search_Frontend_(CS)
 * @ZephyrId MTA-128
 */
class SuggestSearchingResultEntityTest extends Injectable
{
    /**
     * Run suggest searching result test
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogSearchQuery $catalogSearch
     * @return void
     */
    public function testSearch(CmsIndex $cmsIndex, CatalogSearchQuery $catalogSearch)
    {
        $cmsIndex->open();
        $searchData = $catalogSearch->getQueryText();
        $searchData = reset($searchData);
        $cmsIndex->getSearchBlock()->search($searchData['query_text']);
    }
}
