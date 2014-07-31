<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;

/**
 * Class AssertCmsPageVersionInGrid
 *
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
     * Assert that created CMS page version can be found on CMS page Versions tab in grid via:
     * Version label, Owner, Quantity, Access Level
     *
     * @param CmsPage $cms
     * @param CmsNew $cmsNew
     * @param CmsIndex $cmsIndex
     * @param array $results
     * @return void
     */
    public function processAssert(CmsPage $cms, CmsNew $cmsNew, CmsIndex $cmsIndex, array $results)
    {
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->open();
        $cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $cmsNew->getPageForm()->openTab('versions');
        preg_match('/\d+/', $results['revision'], $matches);
        $filter = [
            'label' => $cms->getTitle(),
            'owner' => $results['owner'],
            'access_level' => $results['access_level'],
            'quantity' => $matches[0],
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->isRowVisible($filter),
            'CMS Page Version with '
            . 'label \'' . $filter['label'] . '\', '
            . 'owner \'' . $filter['owner'] . '\', '
            . 'access level \'' . $filter['access_level'] . '\', '
            . 'quantity \'' . $filter['quantity'] . '\', '
            . 'is absent in CMS Page Versions grid.'
        );
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
