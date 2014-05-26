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
     * @param CustomerInjectable $initialCustomer
     * @param CustomerIndex $pageCustomerIndex
     * @return void
     */
    public function processAssert(
        CustomerInjectable $initialCustomer,
        CustomerIndex $pageCustomerIndex
    ) {
        $filter = [
            'email' => $initialCustomer->getEmail()
        ];

        $pageCustomerIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $pageCustomerIndex->getCustomerGridBlock()->isRowVisible($filter),
            'Customer with '
            . 'email \'' . $filter['email'] . '\' '
            . 'is present in Customer grid.'
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
