<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsNew;

/**
 * Class AssertCmsPageInitialVersionInGrid
 * Assert that initial CMS page version can be found on CMS page Versions tab in grid
 */
class AssertCmsPageInitialVersionInGrid extends AssertCmsPageVersionInGrid
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that initial CMS page version can be found on CMS page Versions tab in grid via:
     * Version label, Owner, Quantity, Access Level
     *
     * @param CmsPage $cms
     * @param CmsNew $cmsNew
     * @param CmsIndex $cmsIndex
     * @param array $results
     * @param CmsPage $cmsInitial
     * @return void
     */
    public function processAssert(
        CmsPage $cms,
        CmsNew $cmsNew,
        CmsIndex $cmsIndex,
        array $results,
        CmsPage $cmsInitial = null
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->cmsNew = $cmsNew;
        $filter = ['title' => $cms->getTitle()];
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsNew->getPageForm()->openTab('versions');
        $this->prepareFilter($cmsInitial, $results);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS Page Initial Version is present in grid.';
    }
}
