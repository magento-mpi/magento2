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
use Magento\VersionsCms\Test\Fixture\Version;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;

/**
 * Test Creation for UpdateCmsPageVersionsEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create CMS page under version control
 * 2. Create custom admin
 *
 * Steps:
 * 1. Login to the backend
 * 2. Navigate to Content > Elements: Pages
 * 3. Open the page with 'Version Control' = 'Yes'
 * 4. Open 'Versions' tab
 * 5. Open version on the top of the grid
 * 6. Fill fields according to dataset
 * 7. Click 'Save'
 * 8. Perform appropriate assertions
 *
 * @group CMS_Versioning_(PS)
 * @ZephyrId MAGETWO-26960
 */
class UpdateCmsPageVersionsEntityTest extends Injectable
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
     * CmsVersionEdit Page
     *
     * @var CmsVersionEdit
     */
    protected $cmsVersionEdit;

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CmsNew $cmsNew
     * @param CmsVersionEdit $cmsVersionEdit
     * @return void
     */
    public function __inject(CmsIndex $cmsIndex, CmsNew $cmsNew, CmsVersionEdit $cmsVersionEdit)
    {
        $this->cmsIndex = $cmsIndex;
        $this->cmsNew = $cmsNew;
        $this->cmsVersionEdit = $cmsVersionEdit;
    }

    /**
     * Edit Cms Page Versions Entity
     *
     * @param CmsPage $cms
     * @param Version $version
     * @param string $quantity
     * @return array
     */
    public function test(CmsPage $cms, Version $version, $quantity)
    {
        $this->markTestIncomplete("Bug: MAGETWO-28876");
        // Precondition
        $cms->persist();
        // Steps
        $filter = ['title' => $cms->getTitle()];
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getPageForm()->openTab('versions');
        $filter = ['label' => $cms->getTitle()];
        $this->cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->searchAndOpen($filter);
        $this->cmsVersionEdit->getVersionForm()->fill($version);
        $this->cmsVersionEdit->getFormPageActions()->save();
        return ['results' => [
            'label' => $version->getLabel(),
            'owner' => $version->getUserId(),
            'access_level' => $version->getAccessLevel(),
            'quantity' => $quantity,
            ]
        ];
    }
}
