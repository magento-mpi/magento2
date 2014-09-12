<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CustomerCustomAttributes\Test\Page\Adminhtml\CustomerAttributeNew;

/**
 * Class AssertCustomerCustomAttributeErrorSaveMessage
 * Assert that after customer attribute save error message appears
 */
class AssertCustomerCustomAttributeErrorSaveMessage extends AbstractConstraint
{
    /**
     * Text of save error message
     */
    const ERROR_SAVE_MESSAGE = 'An attribute with this code already exists.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after customer attribute save error message appears
     *
     * @param CustomerAttributeNew $customerAttributeNew
     * @return void
     */
    public function processAssert(CustomerAttributeNew $customerAttributeNew)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_SAVE_MESSAGE,
            $customerAttributeNew->getMessagesBlock()->getErrorMessages(),
            'Wrong error message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Attribute error save message is present after creation attribute with already exist code.';
    }
}
