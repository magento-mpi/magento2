<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
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
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountCreate $customerAccountCreate
     * @param CustomerCustomAttribute $customerAttribute
     * @param CustomerCustomAttribute $initialCustomerAttribute
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CustomerAccountCreate $customerAccountCreate,
        CustomerCustomAttribute $customerAttribute,
        CustomerCustomAttribute $initialCustomerAttribute = null
    ) {
        $customerAttribute = $initialCustomerAttribute === null ? $customerAttribute : $initialCustomerAttribute;
        $cmsIndex->open();
        $cmsIndex->getLinksBlock()->openLink('Register');
        \PHPUnit_Framework_Assert::assertTrue(
            $customerAccountCreate->getCustomerAttributesRegisterForm()->isCustomerAttributeVisible($customerAttribute),
            'Customer Custom Attribute with attribute code: \'' . $customerAttribute->getAttributeCode() . '\' '
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
