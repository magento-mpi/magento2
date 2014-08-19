<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;

/**
 * Class AssertGiftRegistrySuccessDeleteMessageOnBackend
 * Assert message appears after delete gift registry on backend
 */
class AssertGiftRegistrySuccessDeleteMessageOnBackend extends AbstractConstraint
{
    /**
     * Success gift registry delete message
     */
    const SUCCESS_DELETE_MESSAGE = 'You deleted this gift registry entity.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
