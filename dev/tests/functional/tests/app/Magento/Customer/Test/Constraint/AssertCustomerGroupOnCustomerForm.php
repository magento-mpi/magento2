<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerGroup;
use Magento\Customer\Test\Page\Adminhtml\CustomerNew;

/**
 * Class AssertCustomerGroupOnCustomerForm
 *
 * @package Constraint
 */
class AssertCustomerGroupOnCustomerForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that customer group on customer form after customer group save
     *
     * @param CustomerGroup $customerGroup
     * @param CustomerNew $customerNew
     * @internal param Customer $customer
     * @return void
     */
    public function processAssert(
        CustomerGroup $customerGroup,
        CustomerNew $customerNew
    ) {
        $customerNew->open();
        $findOnCustomerForm = $customerNew->getMainForm()->customerGroupNameFind($customerGroup->getData('code'));
        \PHPUnit_Framework_Assert::assertTrue(
            $findOnCustomerForm,
            "Customer group {$customerGroup->getData('code')} not in customer form."
        );
    }

    /**
     * Text of customer group on customer form.
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer group on customer form.';
    }
}
