<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;

/**
 * Class AssertCmsPageRevisionInGrid
 * Assert that created CMS page revision can be found in CMS page Version Revisions grid
 */
class AssertCmsPageRevisionInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created CMS page revision can be found in CMS page Version Revisions grid
     *
     * @param CmsPage $cms
     * @param CmsIndex $cmsIndex
     * @param CmsNew $cmsNew
     * @param CmsVersionEdit $cmsVersionEdit
     * @param array $results
     * @return void
     */
    public function processAssert(
        CmsPage $cms,
        CmsIndex $cmsIndex,
        CmsNew $cmsNew,
        CmsVersionEdit $cmsVersionEdit,
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
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsVersionEdit->getRevisionsGrid()->isRowVisible($filter),
            'CMS Page Revision with '
            . 'revision_number_from \'' . $filter['revision_number_from'] . '\', '
            . 'revision_number_to \'' . $filter['revision_number_to'] . '\', '
            . 'author \'' . $filter['author'] . '\', '
            . 'is not present in CMS Page Revisions grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS Page Revision is present in grid.';
    }
}
