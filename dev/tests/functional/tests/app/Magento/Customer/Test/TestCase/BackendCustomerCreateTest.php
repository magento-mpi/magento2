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
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexNew;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Fixture\AddressInjectable;

/**
 * Test Coverage for CreateCustomerBackendEntity
 *
 * General Flow:
 * 1. Log in as default admin user.
 * 2. Go to Customers > All Customers
 * 3. Press "Add New Customer" button
 * 4. Fill form
 * 5. Click "Save Customer" button
 * 6. Perform all assertions
 *
 * @ticketId MAGETWO-23424
 */
class BackendCustomerCreateTest extends Injectable
{
    /**
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * @var CustomerIndex
     */
    protected $pageCustomerIndex;

    /**
     * @var CustomerIndexNew
     */
    protected $pageCustomerIndexNew;

    /**
     * @param CustomerIndex $pageCustomerIndex
     * @param CustomerIndexNew $pageCustomerIndexNew
     */
    public function __inject(
        CustomerIndex $pageCustomerIndex,
        CustomerIndexNew $pageCustomerIndexNew
    ) {
        $this->pageCustomerIndex = $pageCustomerIndex;
        $this->pageCustomerIndexNew = $pageCustomerIndexNew;
    }

    /**
     * @param CustomerInjectable $customer
     * @param AddressInjectable $address
     */
    public function testBackendCustomerCreate(CustomerInjectable $customer, AddressInjectable $address)
    {
        // Steps
        $this->pageCustomerIndex->open();
        $this->pageCustomerIndex->getPageActions()->addNew();
        $this->pageCustomerIndexNew->getCustomerForm()->fillCustomer($customer, $address);
        $this->pageCustomerIndexNew->getPageActions()->save();
    }
}
