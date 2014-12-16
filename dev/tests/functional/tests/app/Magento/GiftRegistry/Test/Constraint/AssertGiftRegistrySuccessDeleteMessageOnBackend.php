<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftRegistrySuccessDeleteMessageOnBackend
 * Assert message appears after delete gift registry on backend
 */
class AssertGiftRegistrySuccessDeleteMessageOnBackend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success gift registry delete message
     */
    const SUCCESS_DELETE_MESSAGE = 'You deleted this gift registry entity.';

    /**
     * Assert message appears after delete gift registry on backend
     *
     * @param CustomerIndexEdit $customerIndexEdit
     * @return void
     */
    public function processAssert(CustomerIndexEdit $customerIndexEdit)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $customerIndexEdit->getMessagesBlock()->getSuccessMessages(),
            'Wrong success delete message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift registry success delete message is present.';
    }
}
