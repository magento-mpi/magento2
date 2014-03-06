<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\AdminHtml\CmsPageGrid;
use Magento\Cms\Test\Page\CmsPage;
use Magento\Cms\Test\Fixture\CmsPage as CmsPageFixture;

/**
 * Class CmsPageSaveSuccess
 *
 * @package Magento\Backend\Test\Constraint
 */
class CmsPageSaveSuccess extends AbstractConstraint
{
    /**
     * @inheritdoc
     */
    protected $severeness = 'low';

    /**
     * Verify that page has not been created
     *
     * @param CmsPageGrid $cmsPageGrid
     * @param CmsPage $cmsPage
     * @param CmsPageFixture $cmsPageFixture
     * @return void
     */
    public function processAssert(CmsPageGrid $cmsPageGrid, CmsPage $cmsPage, CmsPageFixture $cmsPageFixture)
    {
        $message = $cmsPageGrid->getMessageBlock();
        $result = $message->assertSuccessMessage();
        if ($result) {
            // Find Page in grid
            $cmsPageGridBlock = $cmsPageGrid->getCmsPageGridBlock();
            $cmsPageGridBlock->search(array('title' => $cmsPageFixture->getTitle()));

            // Select the 'Preview' link for the new page
            $cmsPageGridBlock = $cmsPageGrid->getCmsPageGridBlock();
            $cmsPageGridBlock->previewCmsPage();

            // Verify the Cms Page content
            $cmsPage->init($cmsPageFixture);
            $cmsPage->selectWindow();
            $cmsPageBlock = $cmsPage->getCmsPageBlock();

            $message = 'Matched CMS Page Content "' . $cmsPageFixture->getContent() . '" not found on the page';
            $constraint = new \PHPUnit_Framework_Constraint_IsEqual($cmsPageBlock->getPageContent());
            \PHPUnit_Framework_Assert::assertThat($cmsPageFixture->getContent(), $constraint, $message);
        } else {
            $this->fail($cmsPageFixture->getTitle(), 'Cms Page creation failed.');
        }
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return 'saved successfully.';
    }
}
