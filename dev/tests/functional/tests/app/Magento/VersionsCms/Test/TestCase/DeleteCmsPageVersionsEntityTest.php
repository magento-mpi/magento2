<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\TestCase;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\VersionsCms\Test\Fixture\Version;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for DeleteCmsPageVersionsEntityTest
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create CMS page
 * 2. Edit this Page and add new Version:
 *  - Change Name
 *  - Open Revision and click Publish
 *
 * Steps:
 * 1. Login to the backend
 * 2. Navigate to Content > Elements: Pages
 * 3. Open the page
 * 4. Open 'Versions' tab
 * 5. Open the version according to dataset in grid
 * 6. Click on 'Delete' button
 * 8. Perform appropriate assertions
 *
 * @group CMS_Versioning_(PS)
 * @ZephyrId MAGETWO-27290
 */
class DeleteCmsPageVersionsEntityTest extends Injectable
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
     * Delete Cms Page Versions Entity
     *
     * @param CmsPage $cms
     * @param Version $version
     * @param array $results
     * @param string $initialVersionToDelete
     * @return array
     */
    public function test(CmsPage $cms, Version $version, array $results, $initialVersionToDelete)
    {
        $this->markTestIncomplete("Bug: MAGETWO-30362");
        // Precondition
        $cms->persist();
        $filter = ['title' => $cms->getTitle()];
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getPageForm()->openTab('versions');
        $filter = ['label' => $cms->getTitle()];
        $this->cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->searchAndOpen($filter);
        $this->cmsVersionEdit->getVersionForm()->fill($version);
        $this->cmsVersionEdit->getFormPageActions()->saveAsNewVersion();

        // Steps
        $filter = ['title' => $cms->getTitle()];
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getPageForm()->openTab('versions');
        $label = $initialVersionToDelete == 'Yes' ? $cms->getTitle() : $version->getLabel();
        $filter = ['label' => $label];
        $this->cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->searchAndOpen($filter);
        $this->cmsVersionEdit->getFormPageActions()->delete();

        return [
            'results' => [
                'label' => $label,
                'owner' => $results['owner'],
                'access_level' => $results['access_level'],
                'quantity' => $results['quantity'],
            ]
        ];
    }
}
