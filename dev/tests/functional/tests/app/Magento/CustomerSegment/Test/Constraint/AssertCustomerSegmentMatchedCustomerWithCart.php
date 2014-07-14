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
 * Assert that grid on 'Matched Customer' tab contains customer according to conditions,
 * assert number of matched customer near 'Matched Customer(%number%)' should be equal row in grid
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
     * Assert that grid on 'Matched Customer' tab contains customer according to conditions(it need save condition before
     * verification), assert number of matched customer near 'Matched Customer(%number%)' should be equal row in grid
     * with adding product to shopping cart
     *
     * @return void
     */
    public function assert()
    {
        $errors = '';
        $formTabs = $this->customerSegmentNew->getFormTabs();
        $this->customerSegmentIndex->open();
        $this->customerSegmentIndex->getGrid()->searchAndOpen(
            ['grid_segment_name' => $this->customerSegment->getName()]
        );
        $customerSegmentGrid = $formTabs->getMatchedCustomers()->getCustomersGrid();
        $formTabs->openTab('matched_customers');

        if ($customerSegmentGrid->isRowVisible(['email' => $this->customer->getEmail()])) {
            $errors .= "Customer is absent in grid.\n";
        }

        $customerSegmentGrid->resetFilter();
        $totalOnTab = $formTabs->getNumberOfCustomersOnTabs();
        $totalInGrid = $customerSegmentGrid->getTotalRecords();
        if ( $totalOnTab == $totalInGrid) {
            $errors .= 'Wrong count of records is displayed.'
                . "\nExpected: " . $totalInGrid
                . "\nActual: " . $totalOnTab;
        }
        
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
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
