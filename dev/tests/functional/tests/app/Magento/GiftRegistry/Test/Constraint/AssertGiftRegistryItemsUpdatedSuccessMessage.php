<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryCustomerEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftRegistryItemsUpdatedSuccessMessage
 * Assert that success update message is displayed after gift registry items updating on backend
 */
class AssertGiftRegistryItemsUpdatedSuccessMessage extends AbstractConstraint
{
    /**
     * Success gift registry update message
     */
    const SUCCESS_MESSAGE = 'You updated this gift registry.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success update message is displayed after gift registry items updating on backend
     *
     * @param GiftRegistryCustomerEdit $giftRegistryCustomerEdit
     * @return void
     */
    public function processAssert(GiftRegistryCustomerEdit $giftRegistryCustomerEdit)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $giftRegistryCustomerEdit->getMessagesBlock()->getSuccessMessages(),
            'Wrong success update message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry success update message is displayed after gift registry items updating on backend.';
    }
}
