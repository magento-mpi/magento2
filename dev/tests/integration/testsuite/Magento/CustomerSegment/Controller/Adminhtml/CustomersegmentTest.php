<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_CustomerSegment_Controller_Adminhtml_CustomersegmentTest extends Magento_Backend_Utility_Controller
{
    /**
     * Checks that all important blocks are successfully created and rendered.
     */
    public function testNewAction()
    {
        $this->dispatch('backend/admin/customersegment/new/');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('form#edit_form', 1, $body);
        $this->assertSelectCount('#magento_customersegment_segment_tabs', 1, $body);
    }

    /**
     * Checks possibility to save customer segment
     */
    public function testSaveAction()
    {
        $segment = Mage::getModel('Magento_CustomerSegment_Model_Segment');
        $segment->load('Customer Segment 1', 'name');
        $this->dispatch('backend/admin/customersegment/save/id/' . $segment->getId());
        $content = $this->getResponse()->getBody();
        $this->assertNotContains('Unable to save the segment.', $content);
    }

    /**
     * @magentoDataFixture Magento/CustomerSegment/_files/segment.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testMatchActionLogging()
    {
        /** @var Magento_Logging_Model_Event $loggingModel */
        $loggingModel = Mage::getModel('Magento_Logging_Model_Event');
        $result = $loggingModel->load('magento_customersegment', 'event_code');
        $this->assertEmpty($result->getId());

        $segment = Mage::getModel('Magento_CustomerSegment_Model_Segment');
        $segment->load('Customer Segment 1', 'name');
        $this->dispatch('backend/admin/customersegment/match/id/' . $segment->getId());

        $result = $loggingModel->load('magento_customersegment', 'event_code');
        $this->assertNotEmpty($result->getId());
        $expected = serialize(array('general' => __('Matched %1 Customers of Segment %2', 1, $segment->getId())));
        $this->assertEquals($expected, $result->getInfo());
    }
}
