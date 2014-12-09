<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;

/**
 * Class AssertCustomerSegmentNotInGrid
 * Assert that created customer segment not presents in grid
 */
class AssertCustomerSegmentNotInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created customer segment not presents in grid
     *
     * @param CustomerSegment $customerSegment
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @return void
     */
    public function processAssert(CustomerSegment $customerSegment, CustomerSegmentIndex $customerSegmentIndex)
    {
        $customerSegmentIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $customerSegmentIndex->getGrid()->isRowVisible(['grid_segment_name' => $customerSegment->getName()]),
            'Customer Segments is present in grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Segments is absent in grid.';
    }
}
