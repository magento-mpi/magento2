<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Fixture\CmsPage as CmsPageFixture;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;

/**
 * Test Creation for CreateCmsPageEntity
 *
 * Test Flow:
 * Steps:
 * 1. Log in to Backend
 * 2. Navigate to Content > Elements > Pages
 * 3. Start to create new CMS Page
 * 4. Fill out fields data according to data set
 * 5. Save CMS Page
 * 6. Verify created CMS Page
 *
 * @group CMS Content (PS)
 * @ZephyrId MAGETWO-25580
 */
class CreateCmsPageEntityTest extends Injectable
{
    /**
     * CmsIndex page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * CmsNew page
     *
     * @var CmsNew
     */
    protected $cmsNew;

    /**
     * Inject data
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
     * Creating Cms page
     *
     * @param CmsPageFixture $cms
     * return void
     */
    public function test(CmsPageFixture $cms)
    {
        $this->markTestIncomplete('MAGETWO-30362');
        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getPageActionsBlock()->addNew();
        $this->cmsNew->getPageForm()->fill($cms);
        $this->cmsNew->getPageMainActions()->save();
    }
}
