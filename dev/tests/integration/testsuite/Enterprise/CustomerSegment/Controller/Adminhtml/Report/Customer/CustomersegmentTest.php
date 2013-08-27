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

/**
 * @magentoAppArea adminhtml
 */
class Enterprise_CustomerSegment_Controller_Adminhtml_Report_Customer_CustomersegmentTest
    extends Magento_Backend_Utility_Controller
{
    /**
     * Checks if child 'grid' block is found in
     * Enterprise/CustomerSegment/view/adminhtml/report/detail/grid/container.phtml
     *
     * @magentoDataFixture Enterprise/CustomerSegment/_files/segment.php
     */
    public function testSegmentAction()
    {
        $segment = Mage::getModel('Enterprise_CustomerSegment_Model_Segment');
        $segment->load('Customer Segment 1', 'name');

        $this->dispatch('backend/admin/report_customer_customersegment/detail/segment_id/' . $segment->getId());
        $content = $this->getResponse()->getBody();
        $this->assertContains('segmentGridJsObject', $content);
    }
}
