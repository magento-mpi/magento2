<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountCreate;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;

/**
 * Class AssertCustomerCustomAttributeOnCustomerRegister
 * Assert that created customer attribute is available during register customer on frontend
 */
class AssertCustomerCustomAttributeOnCustomerRegister extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created customer attribute is available during register customer on frontend
     *
     * @param CustomerAccountCreate $pageCustomerAccountCreate
     * @param CustomerCustomAttribute $customerAttribute
     * @return void
     */
    public function processAssert(
        CustomerAccountCreate $pageCustomerAccountCreate,
        CustomerCustomAttribute $customerAttribute
    ) {
        $pageCustomerAccountCreate->open();
        $attributeCode = $customerAttribute->getAttributeCode();
        \PHPUnit_Framework_Assert::assertTrue(
            $pageCustomerAccountCreate->getRegisterForm()->isCustomerAttributeVisible($attributeCode),
            'Customer Custom Attribute with attribute code: \'' . $attributeCode . '\' '
            . 'is absent during register customer on frontend.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Attribute is present during register customer on frontend.';
    }
}
