<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CustomerSegment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerSegment_Adminhtml_Report_Customer_CustomersegmentControllerTest
    extends Mage_Backend_Utility_Controller
{
    /**
     * Checks if child 'grid' block is found in
     * Enterprise/CustomerSegment/view/adminhtml/report/detail/grid/container.phtml
     */
    public function testSegmentAction()
    {
        /*
         * add @magentoDataFixture Enterprise/CustomerSegment/_files/segment.php after fix
         */
        $this->markTestIncomplete('Bug MAGE-6535');

        $segment = Mage::getModel('Enterprise_CustomerSegment_Model_Segment');
        $segment->load('Customer Segment 1', 'name');
        $this->dispatch('admin/report_customer_customersegment/detail/segment_id/' . $segment->getId());
        $content = $this->getResponse()->getBody();
        $this->assertContains('segmentGridJsObject', $content);
    }
}
