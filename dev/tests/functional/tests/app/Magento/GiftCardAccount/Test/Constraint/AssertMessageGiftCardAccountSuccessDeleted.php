<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;

/**
 * Class AssertMessageGiftCardAccountSuccessDeleted
 * Assert that message gift card account success deleted
 */
class AssertMessageGiftCardAccountSuccessDeleted extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'This gift card account has been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that message gift card account success deleted
     *
     * @param Index $index
     * @return void
     */
    public function processAssert(Index $index)
    {
        $actualMessage = $index->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Success that message gift card account success deleted
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card account success deleted message is present.';
    }
}
