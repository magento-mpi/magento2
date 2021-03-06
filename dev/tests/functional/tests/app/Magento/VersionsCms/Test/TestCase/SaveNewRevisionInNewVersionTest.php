<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\TestCase;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\VersionsCms\Test\Fixture\Revision;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsRevisionEdit;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for SavingNewRevision in a New Version
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
 * 5. Select 1 revision
 * 6. Change revision content and click "Save in a new version"
 * 7. Enter version name
 * 8. Perform all assertions
 *
 * @group CMS_Versioning_(PS)
 * @ZephyrId MAGETWO-29102
 */
class SaveNewRevisionInNewVersionTest extends Injectable
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
     * @return void
     */
    public function test(CmsPage $cms, Revision $revision, array $revisionData, array $results)
    {
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
        $this->cmsRevisionEdit->getFormPageActions()->saveInNewVersion($results['label']);
    }
}
