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
 * Assert that after customer attribute error duplicate message appears
 */
class AssertCustomerCustomAttributeErrorDuplicateMessage extends AbstractConstraint
{
    /**
     * Text of error duplicate message
     */
    const ERROR_DUPLICATE_MESSAGE = 'An attribute with this code already exists.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after customer attribute error duplicate message appears
     *
     * @param CustomerAttributeNew $customerAttributeNew
     * @return void
     */
    public function processAssert(CustomerAttributeNew $customerAttributeNew)
    {
        $customerAttributeNew->getCustomerCustomAttributesForm()->openTab('properties');
        $errors = $customerAttributeNew->getCustomerCustomAttributesForm()->getTabElement('properties')->getJsErrors();
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_DUPLICATE_MESSAGE,
            $errors['Attribute Code']
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
