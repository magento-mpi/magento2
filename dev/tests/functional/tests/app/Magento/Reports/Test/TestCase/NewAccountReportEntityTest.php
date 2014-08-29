<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Magento\Reports\Test\Page\Adminhtml\CustomerAccounts;
use Mtf\TestCase\Injectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;

/**
 * Test Creation for NewAccountReportEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Delete all existing customers
 * 2. Create customer
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Reports> Customers> New
 * 3. Select time range and report period
 * 4. Click "Refresh button"
 * 5. Perform all assertions
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-27742
 */
class NewAccountReportEntityTest extends Injectable
{
    /**
     * Customer Accounts pages
     *
     * @var CustomerAccounts
     */
    protected $customerAccounts;

    /**
     * Inject pages
     *
     * @param CustomerIndex $customerIndexPage
     * @param CustomerAccounts $customerAccounts
     * @return void
     */
    public function __inject(CustomerIndex $customerIndexPage, CustomerAccounts $customerAccounts)
    {
        $this->customerAccounts = $customerAccounts;
        $customerIndexPage->open();
        $customerIndexPage->getCustomerGridBlock()->massaction([], 'Delete', true, 'Select All');
    }

    /**
     * New Accounts Report
     *
     * @param CustomerInjectable $customer
     * @param array $customersReport
     * @return void
     */
    public function test(CustomerInjectable $customer, array $customersReport)
    {
        // Preconditions
        $customer->persist();
        $customersReport = $this->prepareData($customersReport);

        // Steps
        $this->customerAccounts->open();
        $this->customerAccounts->getGridBlock()->searchAccounts($customersReport);
    }

    /**
     * Prepare data
     *
     * @param array $customersReport
     * @return array
     */
    protected function prepareData(array $customersReport)
    {
        foreach ($customersReport as $name => $reportFilter) {
            if ($name === 'report_period') {
                continue;
            }
            $date = $this->objectManager->create(
                '\Magento\Backend\Test\Fixture\Date',
                ['params' => [], 'data' => ['pattern' => $reportFilter]]
            );
            $customersReport[$name] = $date->getData();
        }
        return $customersReport;
    }
}
