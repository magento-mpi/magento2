<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\Cms\Test\Page\AdminHtml\CmsIndex;
use Magento\VersionsCms\Test\Fixture\Revision;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsRevisionEdit;

/**
 * Test Creation for PublishCmsPageRevisionEntity
 *
 * Precondition:
 * Create CMS page under version control
 *
 * Test Flow:
 * 1. Login to the backend.
 * 2. Navigate to Content > Elements: Pages.
 * 3. Open the page with 'Version Control' = 'Yes'
 * 4. Open 'Versions' tab
 * 5. Open version on the top of the grid
 * 6. Open a revision specified in dataset
 * 7. Fill fields according to dataset
 * 8. Click 'Save'
 * 9. Open the revision created (expected id is specified in dataset)
 * 10. Click 'Publish'
 * 11. Perform appropriate assertions.
 *
 * @group CMS_Versioning_(PS)
 * @ZephyrId MAGETWO-27395
 */
class PublishCmsPageRevisionEntityTest extends Injectable
{
    /**
     * Cms index page
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
     * CmsRevisionEdit Page
     *
     * @var CmsRevisionEdit
     */
    protected $cmsRevisionEdit;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $cms = $fixtureFactory->createByCode('cmsPage', ['dataSet' => 'cms-page-test']);
        $cms->persist();
        return [
            'cms' => $cms
        ];
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CmsNew $cmsNew
     * @param CmsVersionEdit $cmsVersionEdit
     * @param CmsRevisionEdit $cmsRevisionEdit
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CmsNew $cmsNew,
        CmsVersionEdit $cmsVersionEdit,
        CmsRevisionEdit $cmsRevisionEdit
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->cmsNew = $cmsNew;
        $this->cmsVersionEdit = $cmsVersionEdit;
        $this->cmsRevisionEdit = $cmsRevisionEdit;
    }

    /**
     * Publish cms page revision
     *
     * @param CmsPage $cms
     * @param Revision $revision
     * @param int $initialRevision
     * @param array $results
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function test(CmsPage $cms, Revision $revision, $initialRevision, array $results)
    {
        $this->markTestIncomplete("Bug: MAGETWO-28876");
        // Steps
        $this->cmsIndex->open();
        $title = $cms->getTitle();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen(['title' => $title]);
        $this->cmsNew->getPageForm()->openTab('versions');
        $this->cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->searchAndOpen(['label' => $title]);
        $this->cmsVersionEdit->getRevisionsGrid()->searchAndOpen(['revision_number_from' => 1]);
        $this->cmsRevisionEdit->getRevisionForm()->toggleEditor();
        $this->cmsRevisionEdit->getRevisionForm()->fill($revision);
        $this->cmsRevisionEdit->getFormPageActions()->save();
        $filter = [
            'revision_number_from' => $initialRevision,
            'revision_number_to' => $initialRevision,
        ];
        $this->cmsVersionEdit->getRevisionsGrid()->searchAndOpen($filter);
        $this->cmsRevisionEdit->getFormPageActions()->publish();
    }
}
