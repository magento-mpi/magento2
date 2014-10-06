<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;

/**
 * Test Creation for CreateCmsPageVersionsEntity for existing CMS page
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create CMS Page
 *
 * Steps:
 * 1. Login to the backend
 * 2. Navigate to Content > Elements: Pages
 * 3. Open existing page from the grid
 * 4. Change dropdown value "Under Version Control" to "Yes"
 * 5. Fill fields according to dataset
 * 6. Click 'Save Page'
 * 7. Perform appropriate assertions
 *
 * @group CMS_Versioning_(PS)
 * @ZephyrId MAGETWO-26738
 */
class CreateCmsPageVersionsEntityForExistingCmsPageTest extends Injectable
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
     * @param CmsPage $cmsInitial
     * @param CmsPage $cms
     * @param array $results
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function test(CmsPage $cmsInitial, CmsPage $cms, array $results)
    {
        $this->markTestIncomplete("Bug: MAGETWO-28876");
        // Precondition
        $cmsInitial->persist();
        // Steps
        $filter = ['title' => $cmsInitial->getTitle()];
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getPageForm()->fill($cms);
        $this->cmsNew->getPageMainActions()->save();
    }
}
