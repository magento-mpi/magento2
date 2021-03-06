<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\TestCase;

use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentNew;
use Mtf\TestCase\Injectable;

/**
 * Test creation for DeleteCustomerSegmentEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create a customer segment.
 *
 * Steps:
 * 1. Login to backend as admin.
 * 2. Use the main menu "CUSTOMERS"->"Segments".
 * 3. Search and open created segment.
 * 4. Click the "Delete" link.
 * 5. Click the "OK" button.
 * 6. Perform the assertions according to the Data Set.
 *
 * @group Customer_Segments_(CS)
 * @ZephyrId MAGETWO-26791
 */
class DeleteCustomerSegmentEntityTest extends Injectable
{
    /**
     * Customer segment index page
     *
     * @var CustomerSegmentIndex
     */
    protected $customerSegmentIndex;

    /**
     * Customer segment create page
     *
     * @var CustomerSegmentNew
     */
    protected $customerSegmentNew;

    /**
     * Inject pages
     *
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @param CustomerSegmentNew $customerSegmentNew
     * @return void
     */
    public function __inject(CustomerSegmentIndex $customerSegmentIndex, CustomerSegmentNew $customerSegmentNew)
    {
        $this->customerSegmentIndex = $customerSegmentIndex;
        $this->customerSegmentNew = $customerSegmentNew;
    }

    /**
     * Delete Customer Segment
     *
     * @param CustomerSegment $customerSegment
     * @return void
     */
    public function test(CustomerSegment $customerSegment)
    {
        // Precondition
        $customerSegment->persist();

        // Steps
        $this->customerSegmentIndex->open();
        $this->customerSegmentIndex->getGrid()->searchAndOpen(
            [
                'grid_segment_name' => $customerSegment->getName(),
            ]
        );
        $this->customerSegmentNew->getPageMainActions()->delete();
    }
}
