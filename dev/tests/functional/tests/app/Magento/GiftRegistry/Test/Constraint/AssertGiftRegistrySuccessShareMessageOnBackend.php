<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryCustomerEdit;

/**
 * Class AssertGiftRegistrySuccessShareMessageOnBackend
 * Assert that after share Gift Registry on backend successful message appears
 */
class AssertGiftRegistrySuccessShareMessageOnBackend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success gift registry share message on backend
     */
    const SUCCESS_MESSAGE = '%d email(s) were sent.';

    /**
     * Assert that success message is displayed after gift registry has been share on backend
     *
     * @param GiftRegistryCustomerEdit $giftRegistryCustomerEdit
     * @param array $sharingInfo
     * @return void
     */
    public function processAssert(GiftRegistryCustomerEdit $giftRegistryCustomerEdit, $sharingInfo)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, count(explode(",", $sharingInfo['emails']))),
            $giftRegistryCustomerEdit->getMessagesBlock()->getSuccessMessages(),
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
