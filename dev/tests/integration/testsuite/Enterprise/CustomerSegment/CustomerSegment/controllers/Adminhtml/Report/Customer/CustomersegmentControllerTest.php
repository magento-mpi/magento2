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
 * @group module:Enterprise_CustomerSegment
 */
class Enterprise_CustomerSegment_Adminhtml_Report_Customer_CustomersegmentControllerTest
    extends Mage_Adminhtml_Utility_Controller
{
    protected $_segment = null;

    /**
     * @covers Enterprise/CustomerSegment/view/adminhtml/report/detail/grid/container.phtml
     */
    public function testSegmentAction()
    {
        $this->markTestIncomplete('Bug MAGE-6535');
        $this->dispatch('admin/report_customer_customersegment/detail/segment_id/1');
        $content = $this->getResponse()->getBody();
        $this->assertContains('segmentGridJsObject', $content);
    }
}
