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

/**
 * Class AssertCmsPageVersionNotInGrid
 * Assert that created CMS page version can not be found on CMS page Versions tab in grid
 */
class AssertCmsPageVersionNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created CMS page version can not be found on CMS page Versions tab in grid
     *
     * @param CmsPage $cms
     * @param CmsIndex $cmsIndex
     * @param CmsNew $cmsNew
     * @param array $results
     * @return void
     */
    public function processAssert(CmsPage $cms, CmsIndex $cmsIndex, CmsNew $cmsNew, array $results)
    {
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->open();
        $cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $cmsNew->getPageVersionsForm()->openTab('versions');
        $filter = [
            'label' => $results['label'],
            'owner' => $results['owner'],
            'access_level' => $results['access_level'],
            'quantity' => $results['quantity'],
        ];
        \PHPUnit_Framework_Assert::assertFalse(
            $cmsNew->getPageVersionsForm()->getTabElement('versions')->getVersionsGrid()->isRowVisible($filter),
            'CMS Page Version with '
            . 'label \'' . $filter['label'] . '\', '
            . 'owner \'' . $filter['owner'] . '\', '
            . 'access level \'' . $filter['access_level'] . '\', '
            . 'quantity \'' . $filter['quantity'] . '\', '
            . 'is present in CMS Page Versions grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS Page Version is absent in grid.';
    }
}
