<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestCase;

use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\Cms\Test\Fixture\CmsPage;
use Mtf\TestCase\Injectable;

/**
 * Test Coverage for Delete CMS Page Entity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. CMS Page is created
 *
 * Steps:
 * 1. Log in to Backend
 * 2. Navigate to CONTENT > Pages
 * 3. Click on CMS Page from grid
 * 4. Click "Delete Page" button
 * 5. Perform all assertions
 *
 * @group CMS_Content_(PS)
 * @ZephyrId MAGETWO-23291
 */
class DeleteCmsPageEntityTest extends Injectable
{
    /**
     * CMS Index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Edit CMS page
     *
     * @var CmsNew
     */
    protected $cmsNew;

    /**
     * Inject pages
     *
     * @param CmsIndex $cmsIndex
     * @param CmsNew $cmsNew
     * @return void
     */
    public function __inject(CmsIndex $cmsIndex, CmsNew $cmsNew)
    {
        $this->cmsIndex = $cmsIndex;
        $this->cmsNew = $cmsNew;
    }

    /**
     * Delete CMS Page
     *
     * @param CmsPage $cmsPage
     * @return void
     */
    public function test(CmsPage $cmsPage)
    {
        $this->markTestIncomplete('MAGETWO-30362');
        // Preconditions
        $cmsPage->persist();
        $filter = [
            'title' => $cmsPage->getTitle()
        ];

        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getPageMainActions()->delete();
    }
}
