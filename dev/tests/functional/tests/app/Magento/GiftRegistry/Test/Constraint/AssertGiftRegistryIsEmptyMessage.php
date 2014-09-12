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
 * Assert that notice message appears if Gift Registry doesn't have any items
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
     * Assert that notice message appears if Gift Registry doesn't have any items after delete
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
            'Wrong notice message is displayed.'
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
