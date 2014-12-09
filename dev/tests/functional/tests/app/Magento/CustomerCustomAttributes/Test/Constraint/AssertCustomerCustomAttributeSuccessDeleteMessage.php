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
 * Class AssertCustomerCustomAttributeSuccessDeleteMessage
 * Assert that after delete customer attribute successful message appears
 */
class AssertCustomerCustomAttributeSuccessDeleteMessage extends AbstractConstraint
{
    /**
     * Text of delete success message
     */
    const SUCCESS_DELETE_MESSAGE = 'You deleted the customer attribute.';

    /* tags */
     const SEVERITY = 'high';
     /* end tags */

    /**
     * Assert that after delete customer attribute successful message appears
     *
     * @param CustomerAttributeIndex $customerAttributeIndex
     * @return void
     */
    public function processAssert(CustomerAttributeIndex $customerAttributeIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
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
        return 'Customer Attribute success delete message is present.';
    }
}
