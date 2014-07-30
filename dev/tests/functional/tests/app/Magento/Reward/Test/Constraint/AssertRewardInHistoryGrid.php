<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\Reward\Test\Fixture\Reward;
use Magento\Reward\Test\Block\Adminhtml\Edit\Tab\Reward as RewardTab;

/**
 * Class AssertRewardInHistoryGrid
 * Assert that after updating reward balance - it reflects in history grid: check Points, Website, Comment
 */
class AssertRewardInHistoryGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after updating reward balance - it reflects in history grid: check Points, Website, Comment
     *
     * @param CustomerIndexEdit $customerIndexEdit
     * @param CustomerIndex $customerIndex
     * @param CustomerInjectable $customer
     * @param Reward $reward
     * @return void
     */
    public function processAssert(
        CustomerIndexEdit $customerIndexEdit,
        CustomerIndex $customerIndex,
        CustomerInjectable $customer,
        Reward $reward
    ) {
        $filter = ['email' => $customer->getEmail()];
        $customerIndex->getCustomerGridBlock()->searchAndOpen($filter);
        $customerIndexEdit->getCustomerForm()->openTab('reward_points');

        /** @var RewardTab $rewardPointsTab */
        $rewardPointsTab = $customerIndexEdit->getCustomerForm()->getTabElement('reward_points');
        $rewardPointsTab->showRewardPointsHistoryGrid();
        $data = $reward->getData();
        if (isset($data['website_id'])) {
            $data['website_id'] = explode('/', $data['website_id'])[0];
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $rewardPointsTab->getHistoryGrid()->isRowVisible($data, false),
            "Record in Reward Points History Grid was not found."
        );
    }

    /**
     * Returns string representation of assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Record in Reward Points History Grid was found.';
    }
}
