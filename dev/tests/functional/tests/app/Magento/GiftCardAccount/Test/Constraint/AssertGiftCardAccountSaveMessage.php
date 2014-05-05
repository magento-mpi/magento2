<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\GiftCardAccount\Test\Page\Adminhtml\GiftCardAccountIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftCardAccountSaveMessage
 *
 * @package Magento\GiftCardAccount\Test\Constraint
 */
class AssertGiftCardAccountSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You saved the gift card account.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     *  Assert that success message is displayed after gift card account save
     *
     * @param GiftCardAccountIndex $giftCardAccountIndex
     * @return void
     */
    public function processAssert(GiftCardAccountIndex $giftCardAccountIndex)
    {
        $actualMessage = $giftCardAccountIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text that success message is displayed after gift card account save
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card account success save message is present.';
    }
}
