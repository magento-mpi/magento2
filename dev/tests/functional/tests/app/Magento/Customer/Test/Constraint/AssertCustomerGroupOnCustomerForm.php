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
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        CustomerGroup $customerGroup,
        CustomerIndexNew $customerIndexNew
    ) {
        $customer = $fixtureFactory->createByCode('customerInjectable', ['data' => ['group_id' => $customerGroup->getData('code')]]);
        $customerIndexNew->open();
        $findOnCustomerForm = $customerIndexNew->getEditForm()->fill($customer)->isVisible();

        \PHPUnit_Framework_Assert::assertTrue(
            $findOnCustomerForm,
            "Customer group {$customerGroup->getData('code')} not in customer form."
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
