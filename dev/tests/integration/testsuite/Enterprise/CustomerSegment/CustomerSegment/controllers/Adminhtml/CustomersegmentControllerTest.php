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
     * Checks that all important blocks are successfully created and rendered.
     * Checks that instance of customer segment are existed in registry
     *
     * @magentoDataFixture Enterprise/CustomerSegment/_files/segment.php
     */
    public function testEditAction()
    {
        /** @var $segment Enterprise_CustomerSegment_Model_Segment */
        $segment = Mage::registry('_fixture/Enterprise_CustomerSegment_Model_Segment');
        $this->getRequest()->setParam('id', $segment->getId());

        $this->dispatch('admin/customersegment/edit/');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('form#edit_form', 1, $body);
        $this->assertSelectCount('ul#enterprise_customersegment_segment_tabs', 1, $body);

        /** @var $currentSegment Enterprise_CustomerSegment_Model_Segment */
        $currentSegment = Mage::registry('current_customer_segment');
        $this->assertInstanceOf('Enterprise_CustomerSegment_Model_Segment', $currentSegment);
        $this->assertEquals($segment->getId(), $currentSegment->getId());

        /**
        * Delete created customer segment and try to run edit action
        */
        $segment->delete();
        Mage::unregister('application_params');
        $this->dispatch('admin/customersegment/edit/');
        /** @var $session Mage_Adminhtml_Model_Session */
        $session = Mage::getSingleton('Mage_Adminhtml_Model_Session');
        $this->assertCount(1, $session->getMessages()->getErrors());
    }

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
