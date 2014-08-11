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
 * Class AssertGiftRegistrySuccessShareMessage
 * Assert that after share Gift Registry successful message appears
 */
class AssertGiftRegistrySuccessShareMessage extends AbstractConstraint
{
    /**
     * Success gift registry share message
     */
    const SUCCESS_MESSAGE = 'You shared the gift registry for %d emails.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after gift registry has been shared
     *
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param int $recipientsQty
     * @return void
     */
    public function processAssert(GiftRegistryIndex $giftRegistryIndex, $recipientsQty)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $recipientsQty),
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
        return 'Gift registry success share message is present.';
    }
}
