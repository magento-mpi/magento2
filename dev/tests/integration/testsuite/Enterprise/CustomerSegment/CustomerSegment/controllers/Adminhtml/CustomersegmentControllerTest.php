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

class Enterprise_CustomerSegment_Adminhtml_CustomersegmentControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * Checks possibility to save customer segment
     */
    public function testSaveAction()
    {
        $segment = new Enterprise_CustomerSegment_Model_Segment;
        $segment->load('Customer Segment 1', 'name');
        $this->dispatch('admin/customersegment/save/id/' . $segment->getId());
        $content = $this->getResponse()->getBody();
        $this->assertNotContains('Unable to save the segment.', $content);
    }
}
