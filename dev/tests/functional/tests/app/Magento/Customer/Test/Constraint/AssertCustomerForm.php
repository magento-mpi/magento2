<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;

/**
 * Class AssertCustomerInGrid
 *
 * @package Magento\Customer\Test\Constraint
 */
class AssertCustomerForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that displayed customer data on edit page(backend) equals passed from fixture
     *
     * @param CustomerInjectable $customer
     * @param CustomerIndex $pageCustomerIndex
     * @param CustomerIndexEdit $pageCustomerIndexEdit
     * @param AddressInjectable $address [optional]
     * @return void
     */
    public function processAssert(
        CustomerInjectable $customer,
        CustomerIndex $pageCustomerIndex,
        CustomerIndexEdit $pageCustomerIndexEdit,
        AddressInjectable $address = null
    ) {
        $filter = ['email' => $customer->getEmail()];

        $pageCustomerIndex->open();
        $pageCustomerIndex->getCustomerGridBlock()->searchAndOpen($filter);
        \PHPUnit_Framework_Assert::assertTrue(
            $pageCustomerIndexEdit->getCustomerForm()->verifyCustomer($customer, $address),
            'Customer data on edit page(backend) not equals to passed from fixture.'
        );
    }

    /**
     * Text success verify Customer form
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed customer data on edit page(backend) equals to passed from fixture.';
    }
}
