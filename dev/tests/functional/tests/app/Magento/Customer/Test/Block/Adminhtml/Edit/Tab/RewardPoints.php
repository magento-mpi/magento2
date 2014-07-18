<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Adminhtml\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Customer\Test\Block\Adminhtml\Edit\Tab\RewardPoints\Subscription;

/**
 * Class RewardPoints
 * Customer Reward Points edit block
 */
class RewardPoints extends Tab
{
    /**
     * Reward points subscription selector
     *
     * @var string
     */
    protected $subscriptionBlock = '#reward_notification_fieldset';

    /**
     * Return reward points subscription block
     *
     * @return Subscription
     */
    public function getSubscriptionBlock()
    {
        return $this->blockFactory->create(
            '\Magento\Customer\Test\Block\Adminhtml\Edit\Tab\RewardPoints\Subscription',
            ['element' => $this->_rootElement->find($this->subscriptionBlock)]
        );
    }
}
