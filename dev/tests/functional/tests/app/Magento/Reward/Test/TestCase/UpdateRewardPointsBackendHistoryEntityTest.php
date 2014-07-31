<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\Reward\Test\Fixture\Reward;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Backend History RewardPointsEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create customer
 *
 * Steps:
 * 1. Open backend
 * 2. Open created customer in preconditions
 * 3. Fill data from dataSet
 * 4. Click Save and Continue
 * 5. Perform all assertions
 *
 * @group Reward_Points_(CS)
 * @ZephyrId MAGETWO-26683
 */
class UpdateRewardPointsBackendHistoryEntityTest extends Injectable
{
    /**
     * CustomerIndex page
     *
     * @var CustomerIndex
     */
    protected $customerIndex;

    /**
     * CustomerEdit page
     *
     * @var CustomerIndexEdit
     */
    protected $customerIndexEdit;

    /**
     * Preconditions for test
     *
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(CustomerInjectable $customer)
    {
        $customer->persist();
        return ['customer' => $customer];
    }

    /**
     * Page injection for test
     *
     * @param CustomerIndex $customerIndex
     * @param CustomerIndexEdit $customerIndexEdit
     * @return void
     */
    public function __inject(CustomerIndex $customerIndex, CustomerIndexEdit $customerIndexEdit)
    {
        $this->customerIndex = $customerIndex;
        $this->customerIndexEdit = $customerIndexEdit;
    }

    /**
     * Run Test Creation for Backend History RewardPointsEntity
     *
     * @param CustomerInjectable $customer
     * @param Reward $reward
     * @return void
     */
    public function test(CustomerInjectable $customer, Reward $reward)
    {
        $filter = ['email' => $customer->getEmail()];

        // Steps
        $this->customerIndex->open();
        $this->customerIndex->getCustomerGridBlock()->searchAndOpen($filter);
        $this->customerIndexEdit->getCustomerForm()->fill($reward);
        $this->customerIndexEdit->getPageActionsBlock()->save();
    }
}
