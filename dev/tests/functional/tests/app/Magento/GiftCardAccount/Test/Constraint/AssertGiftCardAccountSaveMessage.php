<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftCardAccountSaveMessage
 * Assert that success message is displayed after gift card account save
 */
class AssertGiftCardAccountSaveMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
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
