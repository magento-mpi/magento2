<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\VersionsCms\Test\Fixture\Revision;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsRevisionEdit;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsRevisionPreview;
use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;

/**
 * Class AssertCmsPageRevisionPreview
 * Assert that created CMS page revision content can be found in CMS page revisions preview
 */
class AssertCmsPageRevisionPreview extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created CMS page revision content can be found in CMS page revisions preview
     *
     * @param CmsPage $cms
     * @param Revision $revision
     * @param CmsIndex $cmsIndex
     * @param CmsNew $cmsNew
     * @param CmsVersionEdit $cmsVersionEdit
     * @param CmsRevisionEdit $cmsRevisionEdit
     * @param CmsRevisionPreview $cmsRevisionPreview
     * @param array $results
     * @return void
     */
    public function processAssert(
        CmsPage $cms,
        Revision $revision,
        CmsIndex $cmsIndex,
        CmsNew $cmsNew,
        CmsVersionEdit $cmsVersionEdit,
        CmsRevisionEdit $cmsRevisionEdit,
        CmsRevisionPreview $cmsRevisionPreview,
        array $results
    ) {
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->open();
        $cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $cmsNew->getPageForm()->openTab('versions');
        $filter = ['label' => $cms->getTitle()];
        $cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->searchAndOpen($filter);
        $filter = [
            'revision_number_from' => $results['revision_number_from'],
            'revision_number_to' => $results['revision_number_to'],
            'author' => $results['author'],
        ];
        $cmsVersionEdit->getRevisionsGrid()->searchAndOpen($filter);
        $cmsRevisionEdit->getFormPageActions()->preview();
        $pageContent = $cmsRevisionPreview->getPreviewBlock()->getPageContent();
        $fixtureContent = $revision->getContent();

        \PHPUnit_Framework_Assert::assertEquals(
            $fixtureContent,
            $pageContent,
            'Page content is not equals to expected'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Page content is equal to expected.';
    }
}
