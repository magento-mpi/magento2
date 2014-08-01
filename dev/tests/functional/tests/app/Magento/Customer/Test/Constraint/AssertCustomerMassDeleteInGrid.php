<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerMassDeleteInGrid
 * Check that mass deleted customers availability in Customer Grid
 */
class AssertCustomerMassDeleteInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that mass deleted customers availability in Customer Grid
     *
     * @param CustomerIndex $pageCustomerIndex
     * @param AssertCustomerInGrid $assertCustomerInGrid
     * @param int $customersQtyToDelete
     * @param array $customers
     * @return void
     */
    public function processAssert(
        CustomerIndex $pageCustomerIndex,
        AssertCustomerInGrid $assertCustomerInGrid,
        $customersQtyToDelete,
        $customers
    ) {
        $customers = array_slice($customers, $customersQtyToDelete);
        $count = count($customers);
        for ($i = 1; $i <= $count; $i++) {
            $assertCustomerInGrid->processAssert($customers[$i - 1], $pageCustomerIndex);
        }
    }

    /**
     * Text success exist Customer in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Customers is present in Customer grid.';
    }
}
