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

class Enterprise_CustomerSegment_Adminhtml_CustomersegmentControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * Checks that all important blocks are successfully created and rendered.
     */
    public function testNewAction()
    {
        $this->dispatch('backend/admin/customersegment/new/');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('form#edit_form', 1, $body);
        $this->assertSelectCount('#enterprise_customersegment_segment_tabs', 1, $body);
    }

    /**
     * Checks possibility to save customer segment
     */
    public function testSaveAction()
    {
        $segment = Mage::getModel('Enterprise_CustomerSegment_Model_Segment');
        $segment->load('Customer Segment 1', 'name');
        $this->dispatch('backend/admin/customersegment/save/id/' . $segment->getId());
        $content = $this->getResponse()->getBody();
        $this->assertNotContains('Unable to save the segment.', $content);
    }
}
