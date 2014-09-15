<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class IndexTest extends \Magento\Backend\Utility\Controller
{
    /**
     * Checks that all important blocks are successfully created and rendered.
     *
     * @magentoDbIsolation enabled
     */
    public function testNewAction()
    {
        $this->dispatch('backend/customersegment/index/new/');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('form#edit_form', 1, $body);
        $this->assertSelectCount('#magento_customersegment_segment_tabs', 1, $body);
    }

    /**
     * Checks possibility to save customer segment
     *
     * @magentoDbIsolation enabled
     */
    public function testSaveAction()
    {
        $segment = $this->_objectManager->create('Magento\CustomerSegment\Model\Segment');
        $segment->load('Customer Segment 1', 'name');
        $this->dispatch('backend/customersegment/index/save/id/' . $segment->getId());
        $content = $this->getResponse()->getBody();
        $this->assertNotContains('Unable to save the segment.', $content);
    }

    /**
     * @magentoDataFixture Magento/CustomerSegment/_files/segment.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testMatchActionLogging()
    {
        /** @var \Magento\Logging\Model\Event $loggingModel */
        $loggingModel = $this->_objectManager->create('Magento\Logging\Model\Event');
        $result = $loggingModel->load('magento_customersegment', 'event_code');
        $this->assertEmpty($result->getId());

        $segment = $this->_objectManager->create('Magento\CustomerSegment\Model\Segment');
        $segment->load('Customer Segment 1', 'name');
        $this->dispatch('backend/customersegment/index/match/id/' . $segment->getId());

        $result = $loggingModel->load('magento_customersegment', 'event_code');
        $this->assertNotEmpty($result->getId());
        $expected = serialize(array('general' => __('Matched %1 Customers of Segment %2', 1, $segment->getId())));
        $this->assertEquals($expected, $result->getInfo());
    }
}
