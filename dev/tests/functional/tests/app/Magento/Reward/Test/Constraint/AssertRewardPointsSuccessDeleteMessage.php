<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Reward\Test\Page\Adminhtml\ExchangeRateIndex;

/**
 * Class AssertRewardPointsSuccessDeleteMessage
 * Asserts that success delete message equals to expected message.
 */
class AssertRewardPointsSuccessDeleteMessage extends AbstractConstraint
{
    const DELETE_MESSAGE = 'You deleted the rate.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that success delete message equals to expected message
     *
     * @param ExchangeRateIndex $exchangeRateIndex
     * @return void
     */
    public function processAssert(ExchangeRateIndex $exchangeRateIndex)
    {
        $deletesMessage = $exchangeRateIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::DELETE_MESSAGE,
            $deletesMessage,
            'Wrong delete message is displayed.'
        );
    }

    /**
     * Returns message if delete message equals to expected message.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success delete message on Reward Exchange Rates page is correct.';
    }
}
