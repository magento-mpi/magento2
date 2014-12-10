<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageInGrid
 * Assert that CMS page present in grid and can be found by title
 */
class AssertCmsPageInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that cms page is present in pages grid
     *
     * @param CmsIndex $cmsIndex
     * @param CmsPage $cms
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, CmsPage $cms)
    {
        $filter = [
            'title' => $cms->getTitle(),
        ];
        $cmsIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsIndex->getCmsPageGridBlock()->isRowVisible($filter),
            'Cms page \'' . $cms->getTitle() . '\' is not present in pages grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Cms page is present in pages grid.';
    }
}
