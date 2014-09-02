<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Fixture\CustomerGroupInjectable;

/**
 * Test creation for MassAssignCustomerGroup
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create customer group
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Customers> All Customers
 * 3. Find and select(using checkbox) created customer
 * 4. Select "Assign a Customer Group" from action drop-down
 * 5. Select created customer group
 * 6. Click "Submit" button
 * 7. Perform all assertions
 *
 * @group Customer_Groups_(CS), Customers_(CS)
 * @ZephyrId MAGETWO-27892
 */
class CreationForMassAssignCustomerGroupTest extends Injectable
{
    /**
     * Customer index page
     *
     * @var CustomerIndex
     */
    protected $customerIndex;

    /**
     * Prepare data
     *
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(CustomerInjectable $customer)
    {
        $customer->persist();

        return ['customer' => $customer];
    }

    public function __inject(CustomerIndex $customerIndex)
    {
        $this->customerIndex = $customerIndex;
    }

    public function test(CustomerInjectable $customer, CustomerGroupInjectable $customerGroup, $customersGridActions)
    {
        // Steps
        $customerGroup->persist();
        $this->customerIndex->open();
        $this->customerIndex->getCustomerGridBlock()->massaction(
            [['email' => $customer->getEmail()]],
            [$customersGridActions => $customerGroup->getCustomerGroupCode()]
        );
    }
}
