<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerGroup;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexNew;
use Mtf\Fixture\FixtureFactory;

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
     * @param FixtureFactory $fixtureFactory
     * @param CustomerGroup $customerGroup
     * @param CustomerIndexNew $customerIndexNew
     * @param CustomerIndex $pageCustomerIndex
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        CustomerGroup $customerGroup,
        CustomerIndexNew $customerIndexNew,
        CustomerIndex $pageCustomerIndex
    ) {
        /** @var CustomerInjectable $customer */
        $customer = $fixtureFactory->createByCode(
            'customerInjectable',
            [
                'dataSet' => 'default',
                'data' => ['group_id' => $customerGroup->getCustomerGroupCode()]
            ]
        );
        $name = ($customer->hasData('prefix') ? $customer->getPrefix() . ' ' : '')
            . $customer->getFirstname()
            . ($customer->hasData('middlename') ? ' ' . $customer->getMiddlename() : '')
            . ' ' . $customer->getLastname()
            . ($customer->hasData('suffix') ? ' ' . $customer->getSuffix() : '');
        $filter = [
            'name' => $name,
            'email' => $customer->getEmail(),
        ];

        $customerIndexNew->open();
        $customerIndexNew->getCustomerForm()->fillCustomer($customer);
        $customerIndexNew->getPageActionsBlock()->save();
        $pageCustomerIndex->getCustomerGridBlock()->searchAndOpen($filter);

        \PHPUnit_Framework_Assert::assertTrue(
            $customerIndexNew->getCustomerForm()->verify($customer),
            "Customer group {$customerGroup->getCustomerGroupCode()} not in customer form."
        );
    }

    /**
     * Success assert of customer group find on account information page
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer group find on account information page.';
    }
}
