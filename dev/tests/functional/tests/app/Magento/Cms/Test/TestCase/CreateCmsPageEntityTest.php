<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\TestCase;

use Magento\Cms\Test\Fixture\CmsPage as CmsPageFixture;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Mtf\TestCase\Injectable;

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
        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getPageActionsBlock()->addNew();
        $this->cmsNew->getPageForm()->fill($cms);
        $this->cmsNew->getPageMainActions()->save();
    }
}
