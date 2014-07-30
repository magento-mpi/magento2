<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentNew;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentReportIndex;

/**
 * Test Creation for ReportCustomerSegmentEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Delete all existed customers
 * 2. Delete all existed customers segment
 * 3. Test customers are created
 * 4. Test segments are created
 *
 * Steps:
 * 1. Login to backend as admin
 * 2. Use the main menu "REPORTS" -> "Customers" -> "Segments"
 * 3. Select all customer segments
 * 4. Select "View Combined Report" value for the "Actions" drop-down field
 * 5. Fill data according to data set
 * 6. Click the "Submit" button
 * 7. Perform assertions
 *
 * @group Customer_Segments_(CS)
 * @ZephyrId MAGETWO-26675
 */
class ReportCustomerSegmentEntityTest extends Injectable
{
    /**
     * Customer segment report page
     *
     * @var CustomerSegmentReportIndex
     */
    protected $reportPage;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Fulfillment of the conditions of the test
     *
     * @param CustomerIndex $customerIndexPage
     * @param CustomerSegmentReportIndex $reportPage
     * @param CustomerSegmentIndex $segmentIndexPage
     * @param CustomerSegmentNew $segmentNewPage
     * @return void
     */
    public function __inject(
        CustomerIndex $customerIndexPage,
        CustomerSegmentReportIndex $reportPage,
        CustomerSegmentIndex $segmentIndexPage,
        CustomerSegmentNew $segmentNewPage
    ) {
        $this->reportPage = $reportPage;
        // Preconditions
        // Delete all customer
        $customerIndexPage->open();
        $customerIndexPage->getCustomerGridBlock()->massaction([], 'Delete', true, 'Select All');
        // Delete all customer segment
        $segmentIndexPage->open();
        while ($segmentIndexPage->getGrid()->isFirstRowVisible()) {
            $segmentIndexPage->getGrid()->openFirstRow();
            $segmentNewPage->getPageMainActions()->delete();
        }
    }

    /**
     * Run report customer segment entity test
     *
     * @param string $customers
     * @param string $customerSegments
     * @param array $reportActions
     * @param string $foundCustomers
     * @param string $notFoundCustomers
     * @return array
     */
    public function test($customers, $customerSegments, array $reportActions, $foundCustomers, $notFoundCustomers)
    {
        $result = [];
        if ($foundCustomers !== '-') {
            $result['foundCustomers'] = array_map('intval', explode(',', $foundCustomers));
        }
        if ($notFoundCustomers !== '-') {
            $result['notFoundCustomers'] = array_map('intval', explode(',', $notFoundCustomers));
        }
        // Preconditions
        $result['customers'] = $this->createCustomers($customers);
        $result['customerSegments'] = $this->createCustomerSegments($customerSegments);
        // Steps
        $this->reportPage->open();
        $this->reportPage->getReportGrid()->massaction(
            [],
            $reportActions['massaction'],
            false,
            $reportActions['select']
        );

        return $result;
    }

    /**
     * Create customer
     *
     * @param string $customers
     * @return array
     */
    protected function createCustomers($customers)
    {
        $result = [];
        $customers = array_map('trim', explode(',', $customers));
        foreach ($customers as $value) {
            list($dataSet, $address) = explode('::', $value);
            $customer = $this->fixtureFactory->createByCode(
                'customerInjectable',
                [
                    'dataSet' => $dataSet,
                    'data' => [
                        'address' => ['presets' => $address]
                    ]
                ]
            );
            $customer->persist();
            $result[] = $customer;
        }

        return $result;
    }

    /**
     * Create customer segments
     *
     * @param string $customerSegments
     * @return array
     */
    protected function createCustomerSegments($customerSegments)
    {
        $result = [];
        $customerSegments = array_map('trim', explode(',', $customerSegments));
        foreach ($customerSegments as $dataSet) {
            $customerSegment = $this->fixtureFactory->createByCode('customerSegment', ['dataSet' => $dataSet]);
            $customerSegment->persist();
            $result[] = $customerSegment;
        }

        return $result;
    }
}
