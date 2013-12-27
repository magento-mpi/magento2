<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Model\Invoice;

/**
 * Test class for \Magento\GiftWrapping\Model\Invoice\Giftwrapping
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoiceItemWrapping()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $model = $objectHelper->getObject(
            'Magento\GiftWrapping\Model\Total\Invoice\Giftwrapping',
            array()
        );

        $invoice = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods(array('getAllItems', 'getOrder', '__wakeup', 'setGwItemsPrice', 'setGwItemsBasePrice'))
            ->getMock();

        $item = new \Magento\Object();
        $orderItem = new \Magento\Object(
            array('gw_id' => 1, 'gw_base_price' => 5, 'gw_price' => 10)
        );

        $item->setQty(2)
             ->setOrderItem($orderItem);
        $order = new \Magento\Object();

        $invoice->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue(array($item)));
        $invoice->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($order));
        $invoice->expects($this->once())
            ->method('setGwItemsBasePrice')
            ->with(10);
        $invoice->expects($this->once())
            ->method('setGwItemsPrice')
            ->with(20);

        $model->collect($invoice);

    }
}