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
 * Test Coverage for Update CMS Page Entity
 *
 * Test Flow:
 *
 * Preconditions:
 * CMS Page is created
 *
 * Steps:
 * 1. Log in to Backend.
 * 2. Navigate to Content > Elements > Pages.
 * 3. Click on CMS Page from grid.
 * 4. Edit test value(s) according to data set.
 * 5. Click 'Save' CMS Page.
 * 6. Perform asserts.
 *
 * @group CMS_Content_(PS)
 * @ZephyrId MAGETWO-25186
 */
class UpdateCmsPageEntityTest extends Injectable
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
     * Inject page
     *
     * @param CmsIndex $cmsIndex
     * @param CmsNew $cmsNew
     * @param CmsPage $cmsOriginal
     * @return array
     */
    public function __inject(CmsIndex $cmsIndex, CmsNew $cmsNew, CmsPage $cmsOriginal)
    {
        $cmsOriginal->persist();
        $this->cmsIndex = $cmsIndex;
        $this->cmsNew = $cmsNew;
        return ['cmsOriginal' => $cmsOriginal];
    }

    /**
     * Update CMS Page
     *
     * @param CmsPage $cms
     * @param CmsPage $cmsOriginal
     * @return void
     */
    public function testUpdateCmsPage(CmsPage $cms, CmsPage $cmsOriginal)
    {
        $this->cmsIndex->open();
        $filter = ['title' => $cmsOriginal->getTitle()];
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getNewCmsPageForm()->fill($cms);
        $this->cmsNew->getPageMainActions()->save();
    }
}
