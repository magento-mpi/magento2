<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class CreatePageTest
 *
 * @package Magento\Cms\Test\TestCase
 */
class CreatePageTest extends Functional
{
    /**
     * Login to backend as a precondition to test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Creating CMS content page
     *
     * @ZephyrId MAGETWO-12399
     */
    public function testCreateCmsPage()
    {
        // Fixture, pages and blocks
        $cmsPageFixture = Factory::getFixtureFactory()->getMagentoCmsPage();
        $cmsPageGrid = Factory::getPageFactory()->getAdminCmsPage();
        $cmsPageGrid->open();
        $cmsPageGridBlock = $cmsPageGrid->getCmsPageGridBlock();
        // Create a Cms Page
        $cmsPageGridBlock->addNewCmsPage();
        $cmsPageNew = Factory::getPageFactory()->getAdminCmsPageNew();
        $cmsPageNewForm = $cmsPageNew->getNewCmsPageForm();
        $cmsPageNewForm->fill($cmsPageFixture);
        $cmsPageNewForm->save();
        $message = $cmsPageGrid->getMessageBlock();
        $message->assertSuccessMessage();
        $cmsPageGridBlock = $cmsPageGrid->getCmsPageGridBlock();
        $cmsPageGridBlock->search(array('title' => $cmsPageFixture->getPageTitle()));
        $cmsPageGridBlock = $cmsPageGrid->getCmsPageGridBlock();
        // Select the 'Preview' link for the new page
        $cmsPageGridBlock->previewCmsPage();
        $cmsPage = Factory::getPageFactory()->getCmsPage();
        $cmsPage->init($cmsPageFixture);
        $cmsPage->selectWindow();
        $cmsPageBlock = $cmsPage->getCmsPageBlock();
        // Verify the Cms Page content
        $this->assertEquals(
            $cmsPageBlock->getPageContent(),
            $cmsPageFixture->getPageContent(),
            'Matched CMS Page Content "' . $cmsPageFixture->getPageContent() . '" not found on the page'
        );
    }
}
