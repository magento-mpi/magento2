<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Magento\CustomerCustomAttributes\Test\Page\Adminhtml\CustomerAttributeNew;
use Magento\CustomerCustomAttributes\Test\Page\Adminhtml\CustomerAttributeIndex;

/**
 * Test Creation for DeleteCustomerCustomAttributesEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer attribute
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Stores > Customers
 * 3. Open created customer attribute
 * 4. Click "Delete Attribute"
 * 5. Perform all assertions
 *
 * @group Customer_Attributes_(CS)
 * @ZephyrId MAGETWO-26619
 */
class DeleteCustomerCustomAttributesEntityTest extends Injectable
{
    /**
     * Backend page with the list of customer attributes
     *
     * @var CustomerAttributeIndex
     */
    protected $customerAttributeIndex;

    /**
     * Backend page with new customer attribute form
     *
     * @var CustomerAttributeNew
     */
    protected $customerAttributeNew;

    /**
     * Fixture CustomerCustomAttribute
     *
     * @var CustomerCustomAttribute
     */
    protected $customerCustomAttribute;

    /**
     * Preparing customer
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $customer->persist();

        return ['customer' => $customer];
    }

    /**
     * Injection data
     *
     * @param CustomerAttributeIndex $customerAttributeIndex
     * @param CustomerAttributeNew $customerAttributeNew
     * @return void
     */
    public function __inject(
        CustomerAttributeIndex $customerAttributeIndex,
        CustomerAttributeNew $customerAttributeNew
    ) {
        $this->customerAttributeIndex = $customerAttributeIndex;
        $this->customerAttributeNew = $customerAttributeNew;
    }

    /**
     * Delete custom Customer Attribute
     *
     * @param CustomerCustomAttribute $customerAttribute
     * @return void
     */
    public function test(CustomerCustomAttribute $customerAttribute)
    {
        // Precondition
        $customerAttribute->persist();

        // Steps
        $filter = ['frontend_label' => $customerAttribute->getFrontendLabel()];
        $this->customerAttributeIndex->open();
        $this->customerAttributeIndex->getCustomerCustomAttributesGrid()->searchAndOpen($filter);
        $this->customerAttributeNew->getFormPageActions()->delete();
    }
}
