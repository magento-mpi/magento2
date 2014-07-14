<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

/**
 * Class AssertCustomerSegmentMatchedCustomerWithCart
 * Assert that grid on 'Matched Customer' tab contains customer according to conditions(it need save condition before
 * verification), assert number of matched customer near 'Matched Customer(%number%)' should be equal row in grid
 * with adding product to shopping cart
 */
class AssertCustomerSegmentMatchedCustomerWithCart extends AssertCustomerSegmentPriceRuleApplying
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
     * @return void
     */
    public function assert()
    {
        $filter = [
            'grid_segment_name' => $this->customerSegment->getName(),
        ];
        $this->customerSegmentIndex->open();
        $this->customerSegmentIndex->getGrid()->searchAndOpen($filter);
        $customerSegmentGrid = $this->customerSegmentNew->getFormTabs()->getMatchedCustomers()->getCustomersGrid();
        $this->customerSegmentNew->getFormTabs()->openTab('matched_customers');
        \PHPUnit_Framework_Assert::assertTrue(
            $customerSegmentGrid->isRowVisible(['email' => $this->customer->getEmail()]),
            'Customer is absent in grid.'
        );
        $customerSegmentGrid->resetFilter();
        $totalOnTab = $this->customerSegmentNew->getFormTabs()->getNumberOfCustomersOnTabs();
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
