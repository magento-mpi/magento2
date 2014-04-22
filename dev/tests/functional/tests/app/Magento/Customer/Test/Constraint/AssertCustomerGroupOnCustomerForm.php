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
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexNew;

/**
 * Class AssertCustomerGroupOnCustomerForm
 *
 * @package Magento\Customer\Test\Constraint
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
     * Assert that customer group find on account information page
     *
     * @param CustomerGroup $customerGroup
     * @param CustomerIndexNew $customerIndexNew
     * @return void
     */
    public function processAssert(
        CustomerGroup $customerGroup,
        CustomerIndexNew $customerIndexNew
    ) {
        $customerIndexNew->open();
        $findOnCustomerForm = $customerIndexNew->getEditForm()->fillTabAttribute(
            'account_information',
            'group_id',
            $customerGroup->getData('code')
        );

        \PHPUnit_Framework_Assert::assertTrue(
            $findOnCustomerForm,
            "Customer group {$customerGroup->getData('code')} not in customer form."
        );
    }

    /**
     * Text of customer group find on account information page
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer group find on account information page.';
    }
}
