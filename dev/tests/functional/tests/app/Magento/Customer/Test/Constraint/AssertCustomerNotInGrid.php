<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;

/**
 * Class AssertCustomerNotInGrid
 */
class AssertCustomerNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Asserts that customer is not in customer's grid
     *
     * @param CustomerInjectable $customer
     * @param CustomerIndex $customerIndexPage
     * @param int $customersQtyToDelete
     * @param array $customers
     * @return void
     */
    public function processAssert(
        CustomerInjectable $customer,
        CustomerIndex $customerIndexPage,
        $customersQtyToDelete = null,
        $customers = null
    ) {

        $customerIndexPage->open();
        if ($customers !== null) {
            $this->checkMassDeleteCustomers($customersQtyToDelete, $customerIndexPage, $customers);
        } else {
            $filter = [
                'email' => $customer->getEmail()
            ];

            $customerIndexPage->open();
            \PHPUnit_Framework_Assert::assertFalse(
                $customerIndexPage->getCustomerGridBlock()->isRowVisible($filter),
                "Customer with email {$filter['email']} is present in Customer grid."
            );
        }
    }

    /**
     * Check mass delete Customers in grid
     *
     * @param $customersQtyToDelete
     * @param CustomerIndex $customerIndexPage
     * @param $customers
     * @return void
     */
    protected function checkMassDeleteCustomers($customersQtyToDelete, CustomerIndex $customerIndexPage, $customers)
    {
        for ($i = 0; $i <= $customersQtyToDelete - 1; $i++) {
            \PHPUnit_Framework_Assert::assertFalse(
                $customerIndexPage->getCustomerGridBlock()->isRowVisible(['email' => $customers[$i]['email']]),
                "Customer with email {$customers[$i]['email']} is present in Customer grid."
            );
        }
    }

    /**
     * Success message if Customer not in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer is absent in Customer grid.';
    }
}
