<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Creditmemo\Tax;

/**
 * Test class for \Magento\GiftWrapping\Model\Creditmemo\Tax\Giftwrapping
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    public function testCreditmemoItemTaxWrapping()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $model = $objectHelper->getObject('Magento\GiftWrapping\Model\Total\Creditmemo\Tax\Giftwrapping', array());

        $creditmemo = $this->getMockBuilder(
            'Magento\Sales\Model\Order\Creditmemo'
        )->disableOriginalConstructor()->setMethods(
            array('getAllItems', 'getOrder', '__wakeup', 'setGwItemsBaseTaxAmount', 'setGwItemsTaxAmount')
        )->getMock();

        $item = new \Magento\Framework\Object();
        $orderItem = new \Magento\Framework\Object(
            array('gw_id' => 1, 'gw_base_tax_amount_invoiced' => 5, 'gw_tax_amount_invoiced' => 10)
        );

        $item->setQty(2)->setOrderItem($orderItem);
        $order = new \Magento\Framework\Object();

        $creditmemo->expects($this->any())->method('getAllItems')->will($this->returnValue(array($item)));
        $creditmemo->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $creditmemo->expects($this->once())->method('setGwItemsBaseTaxAmount')->with(10);
        $creditmemo->expects($this->once())->method('setGwItemsTaxAmount')->with(20);

        $model->collect($creditmemo);
    }
}
