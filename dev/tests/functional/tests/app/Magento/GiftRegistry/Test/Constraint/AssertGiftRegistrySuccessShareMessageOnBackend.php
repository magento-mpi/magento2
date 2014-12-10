<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryCustomerEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftRegistrySuccessShareMessageOnBackend
 * Assert that after share Gift Registry on backend successful message appears
 */
class AssertGiftRegistrySuccessShareMessageOnBackend extends AbstractConstraint
{
    /**
     * Success gift registry share message on backend
     */
    const SUCCESS_MESSAGE = '%d email(s) were sent.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
