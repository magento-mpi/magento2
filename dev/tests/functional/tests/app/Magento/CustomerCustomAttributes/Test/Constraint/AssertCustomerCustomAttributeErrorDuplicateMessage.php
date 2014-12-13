<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Magento\CustomerCustomAttributes\Test\Page\Adminhtml\CustomerAttributeNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Assert that after customer attribute error duplicate message appears
 */
class AssertCustomerCustomAttributeErrorDuplicateMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Text of error duplicate message
     */
    const ERROR_DUPLICATE_MESSAGE = 'An attribute with this code already exists.';

    /**
     * Assert that after customer attribute error duplicate message appears
     *
     * @param CustomerAttributeNew $customerAttributeNew
     * @return void
     */
    public function processAssert(CustomerAttributeNew $customerAttributeNew)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_DUPLICATE_MESSAGE,
            $customerAttributeNew->getMessagesBlock()->getErrorMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Attribute error duplicate message appears after creation attribute with already exist code.';
    }
}
