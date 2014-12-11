<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reward\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Reward\Test\Fixture\Reward;
use Magento\Reward\Test\Page\RewardCustomerInfo;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Unsubscribe Reward Points Entity on Frontend
 *
 * Test Flow:
 * Preconditions:
 * 1. Create customer
 *
 * Steps:
 * 1. Login to frontend as customer created in preconditions
 * 2. Open My Accounts->Rewards Points
 * 3. Fill data according to dataSet
 * 4. Perform all asserts
 *
 * @group Product_Attributes_(MX), Reward_Points_(CS)
 * @ZephyrId MAGETWO-26381
 */
class UnsubscribeRewardPointsEntityOnFrontendTest extends Injectable
{
    /**
     * Customer account login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Customer account logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Customer account information page
     *
     * @var RewardCustomerInfo
     */
    protected $rewardCustomerInfo;

    /**
     * Injection data
     *
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param RewardCustomerInfo $rewardCustomerInfo
     * @return void
     */
    public function __inject(
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        RewardCustomerInfo $rewardCustomerInfo
    ) {
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->rewardCustomerInfo = $rewardCustomerInfo;
    }

    /**
     * Unsubscribe frontend reward points entity test
     *
     * @param CustomerInjectable $customer
     * @param Reward $reward
     * @return void
     */
    public function test(CustomerInjectable $customer, Reward $reward)
    {
        // Precondition
        $customer->persist();

        // Steps
        $this->customerAccountLogout->open();
        $this->customerAccountLogin->open();
        $this->customerAccountLogin->getLoginBlock()->login($customer);
        $this->rewardCustomerInfo->getAccountMenuBlock()->openMenuItem('Reward Points');
        $this->rewardCustomerInfo->getRewardPointsBlock()->updateSubscription($reward);
    }
}
