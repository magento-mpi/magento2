<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;

/**
 * Class AssertGiftRegistryTypeSuccessDeleteMessage
 * Assert that success delete message is displayed after gift registry has been deleted
 */
class AssertGiftRegistryTypeSuccessDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success gift registry delete message
     */
    const DELETE_MESSAGE = 'You deleted the gift registry type.';

    /**
     * Assert that success delete message is displayed after gift registry has been deleted
     *
     * @param GiftRegistryIndex $giftRegistryIndex
     * @return void
     */
    public function processAssert(GiftRegistryIndex $giftRegistryIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::DELETE_MESSAGE,
            $giftRegistryIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong delete message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry type success delete message is present.';
    }
}
