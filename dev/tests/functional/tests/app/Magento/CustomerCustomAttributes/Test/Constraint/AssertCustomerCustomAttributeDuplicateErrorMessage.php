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
 * Assert that after customer attribute duplicate error message appears
 */
class AssertCustomerCustomAttributeDuplicateErrorMessage extends AbstractConstraint
{
    /**
     * Text of duplicate error message
     */
    const DUPLICATE_ERROR_MESSAGE = 'An attribute with this code already exists.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after customer attribute duplicate error message appears
     *
     * @param CustomerAttributeNew $customerAttributeNew
     * @return void
     */
    public function processAssert(CustomerAttributeNew $customerAttributeNew)
    {
        $error = $customerAttributeNew->getCustomerCustomAttributesForm()->getAttributeError();
        \PHPUnit_Framework_Assert::assertEquals(
            self::DUPLICATE_ERROR_MESSAGE,
            $error['text'],
            'Wrong error message for ' . $error['label'] . ' is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Attribute duplicate error message appears after creation attribute with already exist code.';
    }
}
