<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Creditmemo;

/**
 * Test class for \Magento\GiftWrapping\Model\Creditmemo\Giftwrapping
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    public function testCreditmemoItemWrapping()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $model = $objectHelper->getObject('Magento\GiftWrapping\Model\Total\Creditmemo\Giftwrapping', []);

        $creditmemo = $this->getMockBuilder(
            'Magento\Sales\Model\Order\Creditmemo'
        )->disableOriginalConstructor()->setMethods(
            ['getAllItems', 'getOrder', '__wakeup', 'setGwItemsBasePrice', 'setGwItemsPrice']
        )->getMock();

        $item = new \Magento\Framework\Object();
        $orderItem = new \Magento\Framework\Object(
            ['gw_id' => 1, 'gw_base_price_invoiced' => 5, 'gw_price_invoiced' => 10]
        );

        $item->setQty(2)->setOrderItem($orderItem);
        $order = new \Magento\Framework\Object();

        $creditmemo->expects($this->any())->method('getAllItems')->will($this->returnValue([$item]));
        $creditmemo->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $creditmemo->expects($this->once())->method('setGwItemsBasePrice')->with(10);
        $creditmemo->expects($this->once())->method('setGwItemsPrice')->with(20);

        $model->collect($creditmemo);
    }
}
