<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftRegistrySuccessSaveMessage
 * Assert that after save a Gift Registry successful message appears
 */
class AssertGiftRegistrySuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success gift registry save message
     */
    const SUCCESS_MESSAGE = 'You saved this gift registry.';

    /**
     * Assert that success message is displayed after gift registry has been created
     *
     * @param GiftRegistryIndex $giftRegistryIndex
     * @return void
     */
    public function processAssert(GiftRegistryIndex $giftRegistryIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $giftRegistryIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry success save message is present.';
    }
}
