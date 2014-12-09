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
 * Class AssertCustomerCustomAttributeSuccessSaveMessage
 * Assert that after customer attribute save successful message appears
 */
class AssertCustomerCustomAttributeSuccessSaveMessage extends AbstractConstraint
{
    /**
     * Text of save success message
     */
    const SUCCESS_SAVE_MESSAGE = 'You saved the customer attribute.';

    /* tags */
     const SEVERITY = 'high';
     /* end tags */

    /**
     * Assert that after customer attribute save successful message appears
     *
     * @param CustomerAttributeIndex $customerAttributeIndex
     * @return void
     */
    public function processAssert(CustomerAttributeIndex $customerAttributeIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
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
        return 'Customer Attribute success create message is present.';
    }
}
