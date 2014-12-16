<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerBalance\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\CustomerBalance\Test\Fixture\CustomerBalance;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerBalanceAmount
 * Assert that customer balance amount is changed
 */
class AssertCustomerBalanceAmount extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that customer balance amount is changed
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
        $customerForm = $customerIndexEdit->getCustomerBalanceForm();
        $customerForm->openTab('store_credit');

        \PHPUnit_Framework_Assert::assertTrue(
            $customerForm->getStoreCreditTab()->isStoreCreditBalance($customerBalance->getBalanceDelta()),
            '"Store Credit Balance" grid not displays total amount of store credit balance.'
        );
    }

    /**
     * Customer balance amount is changed
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer balance amount has been updated.';
    }
}
