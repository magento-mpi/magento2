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
 * Class AssertCustomerInGrid
 *
 */
class AssertCustomerInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert customer availability in Customer Grid
     *
     * @param CustomerInjectable $customer
     * @param CustomerIndex $pageCustomerIndex
     * @param $customersQtyToDelete
     * @param $customers
     * @param CustomerInjectable $initialCustomer [optional]
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function processAssert(
        CustomerInjectable $customer,
        CustomerIndex $pageCustomerIndex,
        $customersQtyToDelete = null,
        $customers = null,
        CustomerInjectable $initialCustomer = null
    ) {
        $pageCustomerIndex->open();
        if ($customers === null) {
            if ($initialCustomer) {
                $customer = $customer->hasData()
                    ? array_merge($initialCustomer->getData(), $customer->getData())
                    : $initialCustomer->getData();
            } else {
                $customer = $customer->getData();
            }
            $name = (isset($customer['prefix']) ? $customer['prefix'] . ' ' : '')
                . $customer['firstname']
                . (isset($customer['middlename']) ? ' ' . $customer['middlename'] : '')
                . ' ' . $customer['lastname']
                . (isset($customer['suffix']) ? ' ' . $customer['suffix'] : '');
            $filter = [
                'name' => $name,
                'email' => $customer['email'],
            ];
            $this->checkCustomersInGrid($pageCustomerIndex, $filter);
        } else {
            $customers = array_slice($customers, $customersQtyToDelete);
            $count = count($customers);
            for ($i = 1; $i <= $count; $i++) {
                $filter = [
                    'name' => $customers[$i - 1]['firstname'] . ' ' . $customers[$i - 1]['lastname'],
                    'email' => $customers[$i - 1]['email'],
                ];
                $this->checkCustomersInGrid($pageCustomerIndex, $filter);
            }
        }
    }

    /**
     * Check customers in grid
     *
     * @param CustomerIndex $pageCustomerIndex
     * @param array $filter
     * $return void
     */
    protected function checkCustomersInGrid(CustomerIndex $pageCustomerIndex, array $filter)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $pageCustomerIndex->getCustomerGridBlock()->isRowVisible($filter),
            'Customer with '
            . 'name \'' . $filter['name'] . '\', '
            . 'email \'' . $filter['email'] . '\' '
            . 'is absent in Customer grid.'
        );
    }

    /**
     * Text success exist Customer in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer is present in Customer grid.';
    }
}
