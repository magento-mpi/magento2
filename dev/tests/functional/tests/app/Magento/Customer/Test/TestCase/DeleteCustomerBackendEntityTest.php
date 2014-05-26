<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Test creation for DeleteCustomerBackendEntity
 *
 * Test Flow:
 * Preconditions:
 * 1.Create customer on the backend
 *
 * Steps:
 * 1. Open backend
 * 2. Go to  Customers - All Customers
 * 3. Search and open created customer according to dataset
 * 4. Fill in data according to dataset
 * 5. Perform all assertions according to dataset
 *
 * @group Customers_(CS)
 * @ZephyrId MAGETWO-24764
 */
class DeleteCustomerBackendEntityTest extends Injectable
{
    /**
     * @var CustomerIndex
     */
    protected $customerIndexPage;

    /**
     * @var CustomerIndexEdit
     */
    protected $customerIndexEditPage;

    /**
     * Preparing pages for test
     *
     * @param CustomerIndex $customerIndexPage
     * @param CustomerIndexEdit $customerIndexEditPage
     */
    public function __inject(
        CustomerIndex $customerIndexPage,
        CustomerIndexEdit $customerIndexEditPage
    ) {
        $this->customerIndexPage = $customerIndexPage;
        $this->customerIndexEditPage = $customerIndexEditPage;
    }

    /**
     * Runs Delete Customer Backend Entity test
     *
     * @param CustomerInjectable $initialCustomer
     */
    public function testDeleteCustomerBackendEntity(
        CustomerInjectable $initialCustomer
    ) {
        // Preconditions:
        $initialCustomer->persist();

        // Steps:
        $filter = ['email' => $initialCustomer->getEmail()];
        $this->customerIndexPage->open();
        $this->customerIndexPage->getCustomerGridBlock()->searchAndOpen($filter);
        $this->customerIndexEditPage->getPageActionsBlock()->delete();
    }
}
