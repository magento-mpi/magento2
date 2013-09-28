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

namespace Magento\CustomerSegment\Controller\Adminhtml\Report\Customer;

/**
 * @magentoAppArea adminhtml
 */
class CustomersegmentTest
    extends \Magento\Backend\Utility\Controller
{
    /**
     * Checks if child 'grid' block is found in
     * Magento/CustomerSegment/view/adminhtml/report/detail/grid/container.phtml
     *
     * @magentoDataFixture Magento/CustomerSegment/_files/segment.php
     */
    public function testSegmentAction()
    {
        $segment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\CustomerSegment\Model\Segment');
        $segment->load('Customer Segment 1', 'name');

        $this->dispatch('backend/admin/report_customer_customersegment/detail/segment_id/' . $segment->getId());
        $content = $this->getResponse()->getBody();
        $this->assertContains('segmentGridJsObject', $content);
    }
}
