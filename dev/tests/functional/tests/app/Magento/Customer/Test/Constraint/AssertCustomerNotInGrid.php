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
 * Check that customer is not in customer's grid
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
     * @return void
     */
    public function processAssert(
        CustomerInjectable $customer,
        CustomerIndex $customerIndexPage
    ) {
        $customerIndexPage->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $customerIndexPage->getCustomerGridBlock()->isRowVisible(['email' => $customer->getEmail()]),
            'Customer with email ' . $customer->getEmail() . 'is present in Customer grid.'
        );
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
