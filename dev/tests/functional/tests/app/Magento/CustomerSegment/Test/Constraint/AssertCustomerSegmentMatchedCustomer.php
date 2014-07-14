<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentNew;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;

/**
 * Class AssertCustomerSegmentMatchedCustomer
 * Assert that grid on 'Matched Customer' tab contains customer according to conditions(it need save condition before
 * verification), assert number of matched customer near 'Matched Customer(%number%)' should be equal row in grid
 */
class AssertCustomerSegmentMatchedCustomer extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that grid on 'Matched Customer' tab contains customer according to conditions,
     * assert number of matched customer near 'Matched Customer(%number%)' should be equal row in grid
     *
     * @param CustomerInjectable $customer
     * @param CustomerSegment $customerSegment
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @param CustomerSegmentNew $customerSegmentNew
     * @return void
     */
    public function processAssert(
        CustomerInjectable $customer,
        CustomerSegment $customerSegment,
        CustomerSegmentIndex $customerSegmentIndex,
        CustomerSegmentNew $customerSegmentNew
    ) {
        $customerSegmentIndex->open();
        $filter = [
            'grid_segment_name' => $customerSegment->getName(),
        ];
        $customerSegmentIndex->getGrid()->searchAndOpen($filter);
        $customerSegmentGrid = $customerSegmentNew->getFormTabs()->getMatchedCustomers()->getCustomersGrid();
        $customerSegmentNew->getFormTabs()->openTab('matched_customers');
        \PHPUnit_Framework_Assert::assertTrue(
            $customerSegmentGrid->isRowVisible(['email' => $customer->getEmail()]),
            'Customer is absent in grid.'
        );
        $customerSegmentGrid->resetFilter();
        $totalOnTab = $customerSegmentNew->getFormTabs()->getNumberOfCustomersOnTabs();
        $totalInGrid = $customerSegmentGrid->getTotalRecords();
        \PHPUnit_Framework_Assert::assertEquals(
            $totalInGrid,
            $totalOnTab,
            'Wrong count of records is displayed.'
            . "\nExpected: " . $totalInGrid
            . "\nActual: " . $totalOnTab
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer in Customer Segment grid. Number of matched customer equal row in grid.';
    }
}
