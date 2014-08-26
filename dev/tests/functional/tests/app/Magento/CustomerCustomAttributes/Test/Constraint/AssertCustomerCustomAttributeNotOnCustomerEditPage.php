<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountEdit;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;

/**
 * Class AssertCustomerCustomAttributeNotOnCustomerEditPage
 * Assert that created customer attribute is absent during edit customer account on frontend
 */
class AssertCustomerCustomAttributeNotOnCustomerEditPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created customer attribute is absent during edit customer account on frontend
     *
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAccountEdit $customerAccountEdit
     * @param CustomerInjectable $customer
     * @param CustomerCustomAttribute $customerAttribute
     * @return void
     */
    public function processAssert(
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountEdit $customerAccountEdit,
        CustomerInjectable $customer,
        CustomerCustomAttribute $customerAttribute
    ) {
        $customerAccountLogin->open();
        $customerAccountLogin->getLoginBlock()->fill($customer);
        $customerAccountLogin->getLoginBlock()->submit();
        $customerAccountIndex->open();
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Account Information');
        \PHPUnit_Framework_Assert::assertFalse(
            $customerAccountEdit->getAccountInfoForm()->isCustomerAttributeVisible($customerAttribute),
            'Customer Custom Attribute with attribute code: \'' . $customerAttribute->getAttributeCode() . '\' '
            . 'is present during register customer on frontend.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Attribute is absent during editing customer account on frontend.';
    }
}
