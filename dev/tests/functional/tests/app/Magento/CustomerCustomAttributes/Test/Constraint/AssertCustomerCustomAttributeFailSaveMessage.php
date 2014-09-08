<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CustomerCustomAttributes\Test\Page\Adminhtml\CustomerAttributeIndex;

/**
 * Class AssertCustomerCustomAttributeFailSaveMessage
 * Assert that after customer attribute save fail message appears
 */
class AssertCustomerCustomAttributeFailSaveMessage extends AbstractConstraint
{
    /**
     * Text of save fail message
     */
    const FAIL_SAVE_MESSAGE = 'An attribute with this code already exists.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after customer attribute save fail message appears
     *
     * @param CustomerAttributeIndex $customerAttributeIndex
     * @return void
     */
    public function processAssert(CustomerAttributeIndex $customerAttributeIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::FAIL_SAVE_MESSAGE,
            $customerAttributeIndex->getMessagesBlock()->getSuccessMessages(),
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
        return 'Customer Attribute fail save message is present.';
    }
}
