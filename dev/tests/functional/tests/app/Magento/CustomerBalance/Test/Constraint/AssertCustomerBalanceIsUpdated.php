<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\CustomerBalance\Test\Fixture\CustomerBalance;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\CustomerBalance\Test\Page\Adminhtml\CustomerIndexEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerBalanceIsUpdated
 */
class AssertCustomerBalanceIsUpdated extends AbstractConstraint
{
    const COMMENT = "By admin: admin. (%s)";

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that customer balance is updated
     *
     * @param CustomerIndex $customerIndex
     * @param CustomerInjectable $customer
     * @param CustomerBalance $customerBalance
     * @param CustomerIndexEdit $customerIndexEdit
     * @return void
     */
    public function processAssert(
        CustomerIndex $customerIndex,
        CustomerInjectable $customer,
        CustomerBalance $customerBalance,
        CustomerIndexEdit $customerIndexEdit
    ) {
        $customerIndex->open();
        $filter = ['email' => $customer->getEmail()];
        $customerIndex->getCustomerGridBlock()->searchAndOpen($filter);
        $customerForm = $customerIndexEdit->getCustomerForm();
        $customerForm->openTab('store_credit');

        \PHPUnit_Framework_Assert::assertTrue(
            $customerForm->getCustomerTab()->isStoreCreditBalance($customerBalance->getBalanceDelta()),
            '"Store Credit Balance" grid not displays total amount of store credit balance.'
        );

        $filter = ['info' => sprintf(self::COMMENT, $customerBalance->getAdditionalInfo())];
        \PHPUnit_Framework_Assert::assertTrue(
            $customerIndexEdit->getBalanceHistoryGrid()->isRowVisible($filter),
            '"Balance History" grid not contains correct information.'
        );
        sleep(15);
    }

    /**
     * Customer balance is updated
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer balance is updated.';
    }
}
