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
    const SUCCESS_SAVE_MESSAGE = 'You saved the customer attribute.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after customer attribute save successful message appears
     *
     * @param CustomerAttributeIndex $customerAttributeIndex
     * @return void
     */
    public function processAssert(CustomerAttributeIndex $customerAttributeIndex)
    {
        $actualMessage = $customerAttributeIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_SAVE_MESSAGE
            . "\nActual: " . $actualMessage
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
