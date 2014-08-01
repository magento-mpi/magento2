<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\VersionsCms\Test\Page\Adminhtml\CmsNew;

/**
 * Class AssertCmsPageVersionInGrid
 * Assert that created CMS page version can be found on CMS page Versions tab in grid
 */
class AssertCmsPageVersionInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'medium';

    /**
     * CmsNew Page
     *
     * @var CmsNew
     */
    protected $cmsNew;

    /**
     * CmsIndex Page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Prepare filter and perform assert
     *
     * @param CmsPage $cms
     * @param array $results
     * @internal param array $filter
     * @return void
     */
    public function prepareFilter(CmsPage $cms, array $results)
    {
        preg_match('/\d+/', $results['revision'], $matches);
        $filter = [
            'label' => $cms->getTitle(),
            'owner' => $results['owner'],
            'access_level' => $results['access_level'],
            'quantity' => $matches[0],
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $this->cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->isRowVisible($filter, false),
            'CMS Page Version with '
            . 'label \'' . $filter['label'] . '\', '
            . 'owner \'' . $filter['owner'] . '\', '
            . 'access level \'' . $filter['access_level'] . '\', '
            . 'quantity \'' . $filter['quantity'] . '\', '
            . 'is absent in CMS Page Versions grid.'
        );
    }

    /**
     * Assert that created CMS page version can be found on CMS page Versions tab in grid via:
     * Version label, Owner, Quantity, Access Level
     *
     * @param CmsPage $cms
     * @param CmsNew $cmsNew
     * @param CmsIndex $cmsIndex
     * @param array $results
     * @param CmsPage $cmsInitial[optional]
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
        $this->prepareFilter($cms, $results);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS Page Version is present in grid.';
    }
}
