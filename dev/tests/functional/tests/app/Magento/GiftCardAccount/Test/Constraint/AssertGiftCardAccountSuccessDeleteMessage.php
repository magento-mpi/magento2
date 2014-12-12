<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftCardAccountSuccessDeleteMessage
 * Assert that message gift card account success deleted
 */
class AssertGiftCardAccountSuccessDeleteMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_DELETE_MESSAGE = 'This gift card account has been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card account delete success message is present
     *
     * @param Index $index
     * @return void
     */
    public function processAssert(Index $index)
    {
        $actualMessage = $index->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong success delete message is displayed.'
        );
    }

    /**
     * Text for successfully gift card account deletion message
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card account success delete message is present.';
    }
}
