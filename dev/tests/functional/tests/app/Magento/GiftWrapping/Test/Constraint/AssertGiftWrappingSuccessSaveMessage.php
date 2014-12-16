<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftWrappingSuccessSaveMessage
 */
class AssertGiftWrappingSuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    const SUCCESS_SAVE_MESSAGE = 'You saved the gift wrapping.';

    /**
     * Assert that success message is displayed after Gift Wrapping saved
     *
     * @param GiftWrappingIndex $giftWrappingIndexPage
     * @return void
     */
    public function processAssert(GiftWrappingIndex $giftWrappingIndexPage)
    {
        $actualMessage = $giftWrappingIndexPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_SAVE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text of Created Gift Wrapping Success Message assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift Wrapping success create message is present.';
    }
}
