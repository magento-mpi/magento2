<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\GiftRegistry\Test\Page\GiftRegistryItems;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftRegistryItemsUpdatedFrontendSuccessMessage
 * Assert that after update a Gift Registry items successful message appears
 */
class AssertGiftRegistryItemsUpdatedFrontendSuccessMessage extends AbstractConstraint
{
    /**
     * Success gift registry items update message
     */
    const SUCCESS_MESSAGE = 'You updated the gift registry items.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after gift registry items has been updated
     *
     * @param GiftRegistryItems $giftRegistryItems
     * @return void
     */
    public function processAssert(GiftRegistryItems $giftRegistryItems)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $giftRegistryItems->getMessagesBlock()->getSuccessMessages(),
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
        return 'Gift registry items success update message is present.';
    }
}
