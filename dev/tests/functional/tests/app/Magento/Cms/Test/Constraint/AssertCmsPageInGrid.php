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
 * Class AssertCmsPageInGrid
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
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsIndex->open()->getCmsPageGridBlock()->isRowVisible($filter),
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
