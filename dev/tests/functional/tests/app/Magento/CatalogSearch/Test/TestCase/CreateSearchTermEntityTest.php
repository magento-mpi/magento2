<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Magento\CatalogSearch\Test\Page\Adminhtml\CatalogSearchEdit;
use Magento\CatalogSearch\Test\Page\Adminhtml\CatalogSearchIndex;

/**
 * Test Creation for CreateSearchTermEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Product is created
 *
 * Steps:
 * 1. Go to backend as admin user
 * 4. Navigate to Marketing->SEO&Search->Search Terms
 * 5. Click "Add New Search Term" button
 * 6. Fill out all data according to dataset
 * 7. Save the Search Term
 * 8. Perform all assertions
 *
 * @group Search_Terms_(MX)
 * @ZephyrId MAGETWO-26165
 */
class CreateSearchTermEntityTest extends Injectable
{
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
     * @param CatalogSearchIndex $indexPage
     * @param CatalogSearchEdit $editPage
     * @return void
     */
    public function __inject(CatalogSearchIndex $indexPage, CatalogSearchEdit $editPage)
    {
        $this->indexPage = $indexPage;
        $this->editPage = $editPage;
    }

    /**
     * Run create search term test
     *
     * @param CatalogSearchQuery $searchTerm
     * @return void
     */
    public function test(CatalogSearchQuery $searchTerm)
    {
        $this->markTestIncomplete('MAGETWO-26170');
        // Steps
        $this->indexPage->open();
        $this->indexPage->getGridPageActions()->addNew();
        $this->editPage->getForm()->fill($searchTerm);
        $this->editPage->getFormPageActions()->save();
    }
}
