<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Constraint;

use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Reward\Test\Fixture\Reward;
use Mtf\Constraint\AbstractAssertForm;

/**
 * Class AssertRewardSubscriptionOnBackend
 * Assert that customer reward subscriptions checkboxes are empty
 */
class AssertRewardSubscriptionOnBackend extends AbstractAssertForm
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that customer reward subscriptions checkboxes are empty
     * On Customers->All Customers->%Customer%->Reward Points tab
     *
     * @param CustomerInjectable $customer
     * @param CustomerIndex $customerIndex
     * @param CustomerIndexEdit $customerIndexEdit
     * @param Reward $reward
     * @return void
     */
    public function processAssert(
        CustomerInjectable $customer,
        CustomerIndex $customerIndex,
        CustomerIndexEdit $customerIndexEdit,
        Reward $reward
    ) {
        $filter = ['email' => $customer->getEmail()];
        $customerIndex->open();
        $customerIndex->getCustomerGridBlock()->searchAndOpen($filter);
        $formData = $customerIndexEdit->getCustomerForm()->getData();
        $fixtureData = $reward->getData();
        $error = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty(
            $error,
            "Reward Points Subscription form was filled incorrectly.\nError:\n" . $error
        );
    }

    /**
     * Returns string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer reward subscriptions checkboxes are unchecked.';
    }
}
