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
 * Class AssertGiftRegistrySuccessAddedItemsMessage
 * Assert that success message is displayed after adding products to gift registry on backend
 */
class AssertGiftRegistrySuccessAddedItemsMessage extends AbstractConstraint
{
    /**
     * Success added to gift registry message
     */
    const SUCCESS_MESSAGE = 'Shopping cart items have been added to gift registry.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after adding products to gift registry on backend
     *
     * @param GiftRegistryCustomerEdit $giftRegistryCustomerEdit
     * @return void
     */
    public function processAssert(GiftRegistryCustomerEdit $giftRegistryCustomerEdit)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $giftRegistryCustomerEdit->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry success message is displayed after adding products to gift registry on backend.';
    }
}
