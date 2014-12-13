<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reward\Test\Constraint;

use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertRewardPointsSuccessDeleteMessage
 * Asserts that success delete message equals to expected message.
 */
class AssertRewardPointsSuccessDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Message about successful deletion reward exchange rate
     */
    const SUCCESS_DELETE_MESSAGE = 'You deleted the rate.';

    /**
     * Asserts that success delete message equals to expected message
     *
     * @param RewardRateIndex $rewardRateIndex
     * @return void
     */
    public function processAssert(RewardRateIndex $rewardRateIndex)
    {
        $actualMessage = $rewardRateIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong delete message is displayed.'
        );
    }

    /**
     * Returns message if delete message equals to expected message
     *
     * @return string
     */
    public function toString()
    {
        return 'Success delete message on Reward Exchange Rates page is correct.';
    }
}
