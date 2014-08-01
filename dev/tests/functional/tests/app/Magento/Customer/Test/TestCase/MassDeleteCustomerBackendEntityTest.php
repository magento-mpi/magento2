<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;

/**
 * Test creation for MassDeleteCustomerBackendEntityTest
 *
 * Test Flow:
 * Preconditions:
 * 1. Create X customers
 *
 * Steps:
 * 1. Open backend
 * 2. Go to  Customers - All Customers
 * 3. Select N customers from preconditions
 * 4. Select in dropdown "Delete"
 * 5. Click Submit button
 * 6. Perform all assertions according to dataset
 *
 * @group Customers_(CS)
 * @ZephyrId MAGETWO-26848
 */
class MassDeleteCustomerBackendEntityTest extends Injectable
{
    /**
     * Customer Index page
     *
     * @var CustomerIndex
     */
    protected $customerIndexPage;

    /**
     * Customer Index Edit page
     *
     * @var CustomerIndexEdit
     */
    protected $customerIndexEditPage;

    /**
     * Preparing pages for test
     *
     * @param CustomerIndex $customerIndexPage
     * @param CustomerIndexEdit $customerIndexEditPage
     * @return void
     */
    public function __inject(CustomerIndex $customerIndexPage, CustomerIndexEdit $customerIndexEditPage)
    {
        $this->customerIndexPage = $customerIndexPage;
        $this->customerIndexEditPage = $customerIndexEditPage;
    }

    /**
     * Runs Delete Customer Backend Entity test
     *
     * @param FixtureFactory $fixtureFactory
     * @param int $customersQty
     * @param int $customersQtyToDelete
     * @return array
     */
    public function test(FixtureFactory $fixtureFactory, $customersQty, $customersQtyToDelete)
    {
        // Preconditions:
        $customers = $this->createCustomers($customersQty, $fixtureFactory);


        $deleteCustomers = [];
        for ($i = 0; $i < $customersQtyToDelete; $i++) {
            $deleteCustomers[] = ['email' => $customers[$i]->getEmail()];
        }
        // Steps:
        $this->customerIndexPage->open();
        $this->customerIndexPage->getCustomerGridBlock()->massaction($deleteCustomers, 'Delete', true);

        return ['customers' => $customers];
    }

    /**
     * Create Customers
     *
     * @param $customersQty
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    protected function createCustomers($customersQty, FixtureFactory $fixtureFactory)
    {
        $customers = [];
        for ($i = 1; $i <= $customersQty; $i++) {
            $customer = $fixtureFactory->createByCode('customerInjectable',['dataSet' => 'default',]);
            $customer->persist();
            $customers[] = $customer;
        }
        return $customers;
    }
}
