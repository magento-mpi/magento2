<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftRegistryTypeSuccessSaveMessage
 * Assert that after save a Gift Registry type success message appears
 */
class AssertGiftRegistryTypeSuccessSaveMessage extends AbstractConstraint
{
    /**
     * Success gift registry type save message
     */
    const SUCCESS_MESSAGE = 'You saved the gift registry type.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after save a Gift Registry type success message appears
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
        return 'Gift register success save message is present.';
    }
}
