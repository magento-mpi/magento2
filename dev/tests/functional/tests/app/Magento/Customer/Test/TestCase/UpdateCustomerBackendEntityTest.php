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
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Test Creation for UpdateCustomerBackendEntity
 *
 * General Flow:
 * 1. Login to backend as admin
 * 2. Navigate to CUSTOMERS->All Customers
 * 3. Open from grid test customer
 * 4. Edit some values, if addresses fields are not presented click 'Add New Address' button
 * 5. Click 'Save' button
 * 6. Perform all assertions
 *
 * @ticketId MAGETWO-23881
 */
class UpdateCustomerBackendEntityTest extends Injectable
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
     * @param CustomerInjectable $preconditionCustomer
     * @param CustomerInjectable $customer
     * @param AddressInjectable $address
     */
    public function testUpdateCustomerBackendEntity(
        CustomerInjectable $preconditionCustomer,
        CustomerInjectable $customer,
        AddressInjectable $address
    ) {
        // Prepare data
        $address = $address->hasData() ? $address : null;

        // Preconditions:
        $preconditionCustomer->persist();

        // Steps
        $filter = ['email' => $preconditionCustomer->getEmail()];
        $this->customerIndexPage->open();
        $this->customerIndexPage->getCustomerGridBlock()->searchAndOpen($filter);
        $this->customerIndexEditPage->getCustomerForm()->updateCustomer($customer, $address);
        $this->customerIndexEditPage->getPageActionsBlock()->save();
    }
}
