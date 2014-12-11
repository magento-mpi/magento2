<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Report\Customer;

/**
 * @magentoAppArea adminhtml
 */
class CustomersegmentTest extends \Magento\Backend\Utility\Controller
{
    /**
     * Checks if child 'grid' block is found in
     * Magento/CustomerSegment/view/adminhtml/templates/report/detail/grid/container.phtml
     *
     * @magentoDataFixture Magento/CustomerSegment/_files/segment.php
     */
    public function testSegmentAction()
    {
        $segment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerSegment\Model\Segment'
        );
        $segment->load('Customer Segment 1', 'name');

        $this->dispatch(
            'backend/customersegment/report_customer_customersegment/detail/segment_id/' . $segment->getId()
        );
        $content = $this->getResponse()->getBody();
        $this->assertContains('segmentGridJsObject', $content);
    }
}
