<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsNew;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;

/**
 * Class AssertCmsPageInitialVersionInGrid
 *
 * Assert that initial CMS page version can be found on CMS page Versions tab in grid
 */
class AssertCmsPageInitialVersionInGrid extends AbstractConstraint
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
     * @param CmsPage $cmsInitial
     * @param CmsPage $cms
     * @param CmsNew $cmsNew
     * @param CmsIndex $cmsIndex
     * @param array $results
     * @return void
     */
    public function processAssert(CmsPage $cmsInitial, CmsPage $cms, CmsNew $cmsNew, CmsIndex $cmsIndex, array $results)
    {
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->open();
        $cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $cmsNew->getPageForm()->openTab('versions');
        preg_match('/\d+/', $results['revision'], $matches);
        $filter = [
            'label' => $cmsInitial->getTitle(),
            'owner' => $results['owner'],
            'access_level' => $results['access_level'],
            'quantity' => $matches[0],
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsNew->getPageForm()->getTabElement('versions')->getVersionsGrid()->isRowVisible($filter),
            'CMS Page Initial Version with '
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
        return 'CMS Page Initial Version is present in grid.';
    }
}
