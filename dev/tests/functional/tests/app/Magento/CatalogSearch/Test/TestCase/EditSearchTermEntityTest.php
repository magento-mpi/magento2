<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\TestCase;

use Mtf\TestCase\Injectable;
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
 * @group Search_Terms_(MX)
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
     * Inject pages
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogSearchIndex $indexPage
     * @param CatalogSearchEdit $editPage
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CatalogSearchIndex $indexPage,
        CatalogSearchEdit $editPage
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->indexPage = $indexPage;
        $this->editPage = $editPage;
    }

    /**
     * Run edit search term test
     *
     * @param CatalogSearchQuery $searchTerm
     * @return void
     */
    public function test(CatalogSearchQuery $searchTerm)
    {
        $this->markTestIncomplete('MAGETWO-26170');
        // Preconditions
        $searchText = $searchTerm->getQueryText();
        // Steps
        $this->cmsIndex->open()->getSearchBlock()->search($searchText);
        $this->indexPage->open()->getGrid()->searchAndOpen(['search_query' => $searchText]);
        $this->editPage->getForm()->fill($searchTerm);
        $this->editPage->getFormPageActions()->save();
    }
}
