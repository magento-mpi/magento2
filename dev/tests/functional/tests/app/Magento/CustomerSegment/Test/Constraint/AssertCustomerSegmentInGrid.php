<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerSegmentInGrid
 * Assert that created customer segment presents in grid
 */
class AssertCustomerSegmentInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created customer segment presents in grid and has correct 'Segment','Status','Website'
     *
     * @param CustomerSegment $customerSegment
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @return void
     */
    public function processAssert(CustomerSegment $customerSegment, CustomerSegmentIndex $customerSegmentIndex)
    {
        $customerSegmentIndex->open();
        $website = $customerSegment->getWebsiteIds();
        $filter = [
            'grid_segment_name' => $customerSegment->getName(),
            'grid_segment_is_active' => $customerSegment->getIsActive(),
            'grid_segment_website' => reset($website),
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $customerSegmentIndex->getGrid()->isRowVisible($filter),
            'Customer Segments is absent in grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Segments is present in grid.';
    }
}
