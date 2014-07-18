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
    protected $subscriptionBlock = '#form-validate';

    /**
     * Return reward points subscription block
     *
     * @return Subscription
     */
    public function getSubscriptionBlock()
    {
        return $this->blockFactory->create(
            '\Magento\Reward\Test\Block\Customer\RewardPoints\Subscription',
            ['element' => $this->_rootElement->find($this->subscriptionBlock)]
        );
    }
}
