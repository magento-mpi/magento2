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
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\VersionsCms\Test\Fixture\Revision;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsRevisionEdit;

/**
 * Test Creation for MassDeleteCmsPageRevisionEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create CMS page
 * 2. Edit this Page and add new Revision:
 *  - Add Content
 *  - Click Save
 *
 * Steps:
 * 1. Login to the backend
 * 2. Navigate to Content > Elements: Pages
 * 3. Open the page
 * 4. Open 'Versions' tab
 * 5. Open Cms Page version
 * 6. Select Revision according to dataset in grid
 * 7. Select 'Delete' in Revisions Mass Actions form
 * 8. Click 'Submit'
 * 9. Perform appropriate assertions
 *
 * @group CMS_Versioning_(PS)
 * @ZephyrId MAGETWO-27239
 */
class MassDeleteCmsPageRevisionEntityTest extends Injectable
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
     * CmsRevisionEdit Page
     *
     * @var CmsRevisionEdit
     */
    protected $cmsRevisionEdit;

    /**
     * Create Cms Page
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $cmsPage = $fixtureFactory->createByCode('cmsPage', ['dataSet' => 'cms-page-test']);
        $cmsPage->persist();

        return ['cms' => $cmsPage];
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
     * Delete Cms Page Versions Entity
     *
     * @param CmsPage $cms
     * @param Revision $revision
     * @param array $results
     * @param string $initialRevision
     * @return array
     */
    public function test(CmsPage $cms, Revision $revision, array $results, $initialRevision)
    {
        $this->markTestIncomplete('MAGETWO-27326, MAGETWO-28876');
        // Precondition
        $filter = ['title' => $cms->getTitle()];
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getPageForm()->openTab('versions');
        $filter = ['label' => $cms->getTitle()];
        $this->cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->searchAndOpen($filter);
        $filter = [
            'revision_number_from' => $initialRevision,
            'revision_number_to' => $initialRevision,
            'author' => $results['author'],
        ];
        $this->cmsVersionEdit->getRevisionsGrid()->searchAndOpen($filter);
        $this->cmsRevisionEdit->getRevisionForm()->toggleEditor();
        $this->cmsRevisionEdit->getRevisionForm()->fill($revision);
        $this->cmsRevisionEdit->getFormPageActions()->save();

        // Steps
        $filter = ['title' => $cms->getTitle()];
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getPageForm()->openTab('versions');
        $filter = ['label' => $cms->getTitle()];
        $this->cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->searchAndOpen($filter);
        $revisions[] = [
            'revision_number_from' => $results['revision_number_from'],
            'revision_number_to' => $results['revision_number_to'],
            'author' => $results['author'],
        ];
        $this->cmsVersionEdit->getRevisionsGrid()->massaction($revisions, 'Delete', true);
    }
}
