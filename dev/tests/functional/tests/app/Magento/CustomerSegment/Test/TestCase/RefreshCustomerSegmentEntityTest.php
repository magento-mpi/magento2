<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentNew;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;

/**
 * Test Creation for RefreshCustomerSegmentEntityTest
 *
 * Test Flow:
 * Preconditions:
 * 1. Delete all existed customers.
 * 2. Test segments are created according to specified predefined dataset.
 * 3. Test customers are created on fronend according to specified predefined dataset.
 *
 * Steps:
 * 1. Login to backend as admin.
 * 2. Use the main menu "CUSTOMERS" -> "Segments"
 * 3. Search and open created segment.
 * 4. Click the "Refresh Segment Data" button.
 * 5. Perform assertions.
 *
 * @group Customer_Segments_(CS)
 * @ZephyrId MAGETWO-26786
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RefreshCustomerSegmentEntityTest extends Injectable
{
    /**
     * Customer segment index page
     *
     * @var CustomerSegmentIndex
     */
    protected $customerSegmentIndex;

    /**
     * Page of create new customer segment
     *
     * @var CustomerSegmentNew
     */
    protected $customerSegmentNew;

    /**
     * Inject pages
     *
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @param CustomerSegmentNew $customerSegmentNew
     * @param CustomerIndex $customerIndexPage
     * @return void
     */
    public function __inject(
        CustomerSegmentIndex $customerSegmentIndex,
        CustomerSegmentNew $customerSegmentNew,
        CustomerIndex $customerIndexPage
    ) {
        $this->customerSegmentIndex = $customerSegmentIndex;
        $this->customerSegmentNew = $customerSegmentNew;
        $customerIndexPage->open();
        $customerIndexPage->getCustomerGridBlock()->massaction([], 'Delete', true, 'Select All');
    }

    /**
     * Refresh Customer Segment Entity
     *
     * @param CustomerInjectable $customer
     * @param CustomerSegment $customerSegmentOriginal
     * @return void
     */
    public function test(
        CustomerInjectable $customer,
        CustomerSegment $customerSegmentOriginal
    ) {
        //Preconditions
        $customer->persist();
        $customerSegmentOriginal->persist();

        //Steps
        $this->customerSegmentIndex->open();
        $this->customerSegmentIndex->getGrid()->searchAndOpen(
            [
                'grid_segment_name' => $customerSegmentOriginal->getName(),
            ]
        );
        $this->customerSegmentNew->getPageMainActions()->refreshSegmentData();
    }
}
