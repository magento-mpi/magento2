<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Customer;

use Mtf\Block\Block;
use Magento\Reward\Test\Block\Customer\RewardPoints\Subscription;

/**
 * Class RewardPoints
 * Reward Points block
 */
class RewardPoints extends Block
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
    public function getSubscriptionForm()
    {
        return $this->blockFactory->create(
            '\Magento\Reward\Test\Block\Customer\RewardPoints\Subscription',
            ['element' => $this->_rootElement->find($this->subscriptionForm)]
        );
    }
}
