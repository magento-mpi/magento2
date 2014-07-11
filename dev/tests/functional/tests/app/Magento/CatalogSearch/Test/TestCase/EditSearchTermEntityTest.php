<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Magento\CatalogSearch\Test\Page\Adminhtml\CatalogSearchEdit;
use Magento\CatalogSearch\Test\Page\Adminhtml\CatalogSearchIndex;

/**
 * Test Creation for EditSearchTermEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Product is created
 *
 * Steps:
 * 1. Go to frontend
 * 2. Test word into the Search field at the top of the page and click Go
 * 3. Go to backend as admin user
 * 4. Navigate to Marketing->SEO&Search->Search Terms
 * 5. Click "Edit" link of just added test word search term
 * 6. Fill out all data according to dataset
 * 7. Save the Search Term
 * 8. Perform all assertions
 *
 * @group Search Terms (MX)
 * @ZephyrId MAGETWO-26100
 */
class EditSearchTermEntityTest extends Injectable
{
    /**
     * CMS index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Search term page
     *
     * @var CatalogSearchIndex
     */
    protected $indexPage;

    /**
     * Search term edit page
     *
     * @var CatalogSearchEdit
     */
    protected $editPage;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare data
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogSearchIndex $indexPage
     * @param CatalogSearchEdit $editPage
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(
        CmsIndex $cmsIndex,
        CatalogSearchIndex $indexPage,
        CatalogSearchEdit $editPage,
        FixtureFactory $fixtureFactory
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->indexPage = $indexPage;
        $this->editPage = $editPage;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Run suggest searching result test
     *
     * @param CatalogSearchQuery $catalogSearch
     * @param array $termSearch
     * @return array
     */
    public function test(CatalogSearchQuery $catalogSearch, array $termSearch)
    {
        // Preconditions
        $searchText = $catalogSearch->getQueryText();
        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getSearchBlock()->search($searchText);
        $this->indexPage->open()->getGrid()->searchAndOpen(['search_query' => $searchText]);
        $termSearch['query_text']['value'] = $searchText;
        $termSearch = $this->fixtureFactory->createByCode('catalogSearchQuery', ['data' => $termSearch]);
        $this->editPage->getForm()->fill($termSearch);
        $this->editPage->getFormPageActions()->save();

        return ['termSearch' => $termSearch];
    }
}
