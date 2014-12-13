<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

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
            'title' => $cmsPage->getTitle(),
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
