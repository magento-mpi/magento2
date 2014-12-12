<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\TestCase;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for CreateCmsPageVersionsEntity for a new CMS page
 *
 * Test Flow:
 * Steps:
 * 1. Login to the backend
 * 2. Navigate to Content > Elements: Pages
 * 3. Click 'Add New Page'
 * 4. Fill fields according to dataset
 * 6. Click 'Save Page'
 * 7. Perform appropriate assertions
 *
 * @group CMS_Versioning_(PS)
 * @ZephyrId MAGETWO-26995
 */
class CreateCmsPageVersionsEntityForNewCmsPageTest extends Injectable
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
     * Injection data
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
     * Create CMS Page Version Entity
     *
     * @param CmsPage $cms
     * @param array $results
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function test(CmsPage $cms, array $results)
    {
        $this->markTestIncomplete("Bug: MAGETWO-30362");
        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getPageActionsBlock()->addNew();
        $this->cmsNew->getPageForm()->fill($cms);
        $this->cmsNew->getPageMainActions()->save();
    }
}
