<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reward\Test\Constraint;

use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertRateSuccessSaveMessage
 * Assert that message is present on page
 */
class AssertRateSuccessSaveMessage extends AbstractConstraint
{
    /**
     * Message after success saved Exchange Rate
     */
    const SUCCESS_SAVE_MESSAGE = 'You saved the rate.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that specified message is present on page
     *
     * @param RewardRateIndex $gridPage
     * @return void
     */
    public function processAssert(RewardRateIndex $gridPage)
    {
        $actualMessage = $gridPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Reward points success create message is present and equals to expected.';
    }
}
