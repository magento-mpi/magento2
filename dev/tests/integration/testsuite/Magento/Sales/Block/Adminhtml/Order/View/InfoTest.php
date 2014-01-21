<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Order\View;

/**
 * Test class for \Magento\Sales\Block\Adminhtml\Order\View\Info
 */
class InfoTest extends \Magento\Backend\Utility\Controller
{
    public function testCustomerGridAction()
    {
        $layout = $this->_objectManager->get('Magento\View\LayoutInterface');
        $infoBlock = $layout->createBlock(
            'Magento\Sales\Block\Adminhtml\Order\View\Info',
            'info_block' . rand(), []
        );

        $result = $infoBlock->getCustomerAccountData();
        $this->assertEquals([], $result, 'Customer has additional account data.');
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testGetCustomerGroupName()
    {
        $registry = $this->getMockBuilder('Magento\Core\Model\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $order = $this->_objectManager->get('Magento\Sales\Model\Order')
            ->load('100000001')
            ->setCustomerGroupId(0);

        $registry->expects($this->any())
            ->method('registry')
            ->with('current_order')
            ->will($this->returnValue($order));

        $layout = $this->_objectManager->get('Magento\View\LayoutInterface');
        $customerGroupBlock = $layout->createBlock(
            'Magento\Sales\Block\Adminhtml\Order\View\Info',
            'info_block' . rand(), ['registry' => $registry]
        );

        $result = $customerGroupBlock->getCustomerGroupName();
        $this->assertEquals('NOT LOGGED IN', $result);
    }
}
