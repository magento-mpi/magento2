<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftRegistry\Test\Page\GiftRegistryItems;

/**
 * Class AssertGiftRegistryIsEmptyMessage
 * Assert that after delete a Gift Registry all items Gift Registry has no items message appears
 */
class AssertGiftRegistryIsEmptyMessage extends AbstractConstraint
{
    /**
     * Gift registry info message
     */
    const INFO_MESSAGE = 'This gift registry has no items.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Gift Registry has no items message is displayed after gift registry all items have been deleted
     *
     * @param GiftRegistryItems $giftRegistryItems
     * @return void
     */
    public function processAssert(GiftRegistryItems $giftRegistryItems)
    {
        $giftRegistryItems->open();
        \PHPUnit_Framework_Assert::assertEquals(
            self::INFO_MESSAGE,
            $giftRegistryItems->getGiftRegistryItemsBlock()->getInfoMessage(),
            'Wrong info message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry has no items message is present after gift registry all items have been deleted.';
    }
}
