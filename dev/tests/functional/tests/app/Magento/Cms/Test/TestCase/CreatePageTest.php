<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class CreatePageTest
 */
class CreatePageTest extends Functional
{
    /**
     * Login to backend as a precondition to test
     *
     * @return void
     */
    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-29634');
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Creating CMS content page
     *
     * @ZephyrId MAGETWO-12399
     * @return void
     */
    public function testCreateCmsPage()
    {
        // Fixture, pages and blocks
        $cmsPageFixture = Factory::getFixtureFactory()->getMagentoCmsPage();
        $cmsPageGrid = Factory::getPageFactory()->getAdminCmsPageIndex();
        $cmsPageGrid->open();
        $cmsPageGrid->getPageActionsBlock()->addNew();
        // Create a Cms Page
        $cmsPageNew = Factory::getPageFactory()->getAdminCmsPageNew();
        $cmsPageNewForm = $cmsPageNew->getPageForm();
        $cmsPageNewForm->fill($cmsPageFixture);
        $cmsPageNew->getPageMainActions()->save();
        $message = $cmsPageGrid->getMessagesBlock();
        $message->waitSuccessMessage();
        $cmsPageGridBlock = $cmsPageGrid->getCmsPageGridBlock();
        // Select the 'Preview' link for the new page
        $filter = ['title' => $cmsPageFixture->getPageTitle()];
        $cmsPageGridBlock->searchAndPreview($filter);

        $cmsPage = Factory::getPageFactory()->getCmsPage();
        $browser = Factory::getClientBrowser();
        $browser->selectWindow();
        $cmsPageBlock = $cmsPage->getCmsPageBlock();
        // Verify the Cms Page content
        $this->assertContains(
            $cmsPageFixture->getPageContent(),
            $cmsPageBlock->getPageContent(),
            'Matched CMS Page Content "' . $cmsPageFixture->getPageContent() . '" not found on the page'
        );
    }
}
