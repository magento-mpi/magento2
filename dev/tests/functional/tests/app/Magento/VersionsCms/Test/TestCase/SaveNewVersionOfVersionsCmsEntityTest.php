<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\TestCase;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\VersionsCms\Test\Fixture\Version;
use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;

/**
 * Test Creation for SaveNewVersion of VersionsCmsEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create CMS page
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Content > Pages
 * 3. Find and open created page
 * 4. Go to Versions tab and open default version
 * 5. Change version label, access level, user and click "Save as new version"
 * 6. Perform all assertions
 *
 * @group CMS_Versioning_(PS)
 * @ZephyrId MAGETWO-28574
 */
class SaveNewVersionOfVersionsCmsEntityTest extends Injectable
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
     * Run Save New Version Of Versions Cms Entity Test
     *
     * @param CmsPage $cms
     * @param Version $version
     * @param string $quantity
     * @return array
     */
    public function test(CmsPage $cms, Version $version, $quantity)
    {
        $this->markTestIncomplete("Bug: MAGETWO-28876");
        // Preconditions:
        $cms->persist();

        // Steps:
        $filter = ['title' => $cms->getTitle()];
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getPageForm()->openTab('versions');
        $filter = ['label' => $cms->getTitle()];
        $this->cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->searchAndOpen($filter);
        $this->cmsVersionEdit->getVersionForm()->fill($version);
        $this->cmsVersionEdit->getFormPageActions()->saveAsNewVersion();

        return [
            'results' => [
                'label' => $version->getLabel(),
                'owner' => $version->getUserId(),
                'access_level' => $version->getAccessLevel(),
                'quantity' => $quantity,
            ]
        ];
    }
}
