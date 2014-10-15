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
use Magento\VersionsCms\Test\Fixture\Revision;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsRevisionEdit;

/**
 * Test Creation for UpdateCmsPageRevisionEntityTest
 *
 * Test Flow:
 * Precondition:
 * 1. Create CMS page under version control
 *
 * Steps:
 * 1. Login to the backend.
 * 2. Navigate to Content > Elements: Pages.
 * 3. Open the page with 'Version Control' = 'Yes'
 * 4. Open 'Versions' tab
 * 5. Open version on the top of the grid
 * 6. Open a revision specified in dataset
 * 7. Fill fields according to dataset
 * 8. Click 'Save'
 * 9. Perform appropriate assertions.
 *
 * @group CMS_Versioning_(PS)
 * @ZephyrId MAGETWO-27566
 */
class UpdateCmsPageRevisionEntityTest extends Injectable
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
     * Run UpdateCmsPageRevisionEntity test
     *
     * @param CmsPage $cms
     * @param Revision $revision
     * @param array $revisionData
     * @param array $results
     * @return array
     */
    public function test(CmsPage $cms, Revision $revision, array $revisionData, array $results)
    {
        $this->markTestIncomplete('MAGETWO-28602, MAGETWO-28876');
        // Precondition:
        $cms->persist();
        $title = $cms->getTitle();

        // Steps:
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen(['title' => $title]);
        $this->cmsNew->getPageForm()->openTab('versions');
        $this->cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->searchAndOpen(['label' => $title]);
        $filter = [
            'revision_number_from' => $revisionData['from'],
            'revision_number_to' => $revisionData['to'],
        ];
        $this->cmsVersionEdit->getRevisionsGrid()->searchAndOpen($filter);
        $this->cmsRevisionEdit->getRevisionForm()->fill($revision);
        $this->cmsRevisionEdit->getFormPageActions()->save();

        $results['label'] = $title;
        return ['results' => $results];
    }
}
