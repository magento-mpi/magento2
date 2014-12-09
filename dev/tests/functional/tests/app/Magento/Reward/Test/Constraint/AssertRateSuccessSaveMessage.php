<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;

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

    /* tags */
     const SEVERITY = 'high';
     /* end tags */

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
