<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Constraint;

use Magento\Reward\Test\Page\RewardCustomerInfo;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertRewardSubscriptionSaveMessage
 * Assert that success save message is present on page
 */
class AssertRewardSubscriptionSaveMessage extends AbstractConstraint
{
    const SUCCESS_SAVE_MESSAGE = 'You saved the settings.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that reward points subscription settings success save message is present on page
     *
     * @param RewardCustomerInfo $rewardCustomerInfo
     * @return void
     */
    public function processAssert(RewardCustomerInfo $rewardCustomerInfo)
    {
        $actualMessage = $rewardCustomerInfo->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Reward points subscription settings success save message is present.';
    }
}
