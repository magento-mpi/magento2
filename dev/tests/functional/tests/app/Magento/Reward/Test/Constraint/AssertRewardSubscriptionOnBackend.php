<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reward\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\Reward\Test\Fixture\Reward;
use Mtf\Constraint\AbstractAssertForm;

/**
 * Class AssertRewardSubscriptionOnBackend
 * Assert that customer reward subscriptions checkboxes are empty
 */
class AssertRewardSubscriptionOnBackend extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
