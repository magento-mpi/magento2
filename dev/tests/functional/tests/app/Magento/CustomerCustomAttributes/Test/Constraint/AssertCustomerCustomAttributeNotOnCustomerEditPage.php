<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountEdit;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Mtf\Constraint\AbstractConstraint;
use Mtf\ObjectManager;

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
     * @param ObjectManager $objectManager
     * @return void
     */
    public function processAssert(
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountEdit $customerAccountEdit,
        CustomerInjectable $customer,
        CustomerCustomAttribute $customerAttribute,
        ObjectManager $objectManager
    ) {
        $customerAccountLogin->open();
        $customerAccountLogin->getLoginBlock()->fill($customer);
        $customerAccountLogin->getLoginBlock()->submit();
        $customerAccountIndex->open();
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Account Information');
        $isCustomerAttributeVisible = $customerAccountEdit->getAccountInfoForm()
            ->isCustomerAttributeVisible($customerAttribute);
        $objectManager->create('\Magento\Customer\Test\TestStep\LogoutCustomerOnFrontendStep')->run();
        \PHPUnit_Framework_Assert::assertFalse(
            $isCustomerAttributeVisible,
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
