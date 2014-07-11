<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductCompareItemsLinkIsAbsent
 */
class AssertProductCompareItemsLinkIsAbsent extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert compare products link is NOT visible at the top of page.
     *
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex)
    {
        \PHPUnit_Framework_Assert::assertFalse(
            $cmsIndex->getLinksBlock()->getQtyInCompareList(),
            'That link "Compare Products..." is NOT visible at the top of page.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'That link is is NOT visible at the top of page.';
    }
}
