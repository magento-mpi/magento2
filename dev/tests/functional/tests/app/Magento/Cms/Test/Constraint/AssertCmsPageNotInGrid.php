<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Fixture\CmsPage;

/**
 * Class AssertCmsPageNotInGrid
 */
class AssertCmsPageNotInGrid extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that Cms page is not present in pages grid
     *
     * @param CmsIndex $cmsIndex
     * @param CmsPage $cmsPage
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, CmsPage $cmsPage)
    {
        $filter = [
            'title' => $cmsPage->getTitle()
        ];
        \PHPUnit_Framework_Assert::assertFalse(
            $cmsIndex->getCmsPageGridBlock()->isRowVisible($filter),
            'Cms page \'' . $cmsPage->getTitle() . '\' is present in pages grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Cms page is not present in pages grid.';
    }
}
