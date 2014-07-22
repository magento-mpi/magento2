<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Customer;

use Magento\Reward\Test\Fixture\Reward as RewardFixture;
use Mtf\Block\Block;
use Magento\Reward\Test\Block\Customer\RewardPoints\Subscription;

/**
 * Class RewardPoints
 * Reward Points block in customer My account on frontend
 */
class Reward extends Block
{
    /**
     * Reward points subscription selector
     *
     * @var string
     */
    protected $subscriptionForm = '#form-validate';

    /**
     * Return reward points subscription form
     *
     * @return Subscription
     */
    protected function getSubscriptionForm()
    {
        return $this->blockFactory->create(
            '\Magento\Reward\Test\Block\Customer\RewardPoints\Subscription',
            ['element' => $this->_rootElement->find($this->subscriptionForm)]
        );
    }

    /**
     * Update subscription on customer frontend account
     *
     * @param Reward $reward
     * @return void
     */
    public function updateSubscription(Reward $reward)
    {
        $this->getSubscriptionForm()->fill($reward);
        $this->getSubscriptionForm()->clickSaveButton();
    }

    /**
     * Reward info selector
     *
     * @var string
     */
    protected $rewardInfo = '.reward-rates';

    /**
     * Returns Reward points balance Information
     *
     * @return array|string
     */
    public function getBalanceInformation()
    {
        return $this->_rootElement->find($this->rewardInfo)->getText();
    }
}
