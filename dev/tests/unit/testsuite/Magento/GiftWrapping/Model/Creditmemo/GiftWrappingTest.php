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

namespace Magento\GiftWrapping\Model;

/**
 * Test class for \Magento\GiftWrapping\Model
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    public function testCreditMemoItemTaxWrapping()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $model = $objectHelper->getObject(
            'Magento\GiftWrapping\Model\Total\Creditmemo\Giftwrapping',
            array()
        );


        $creditmemo = $this->getMock('Magento\Sales\Model\Order\CreditMemo', array('getOrder'), array(), '', false);
//        $creditmemo = $this->getMockBuilder('Magento\Sales\Model\Order\CreditMemo')
//            ->disableOriginalConstructor()
//            ->setMethods(array('getAllItems', 'getOrder', /*'setBaseGrandTotal', 'getBaseGrandTotal',
//                'getGwBasePrice', 'getGwCardBasePrice', 'setGrandTotal', 'getGrandTotal',
//                'getGwItemsPrice', 'getGwPrice', 'getGwCardPrice', 'setBaseCustomerBalanceReturnMax',
//                'getBaseCustomerBalanceReturnMax', 'setCustomerBalanceReturnMax', 'getCustomerBalanceReturnMax',*/
//                ))
//            ->getMock();

        $item = new \Magento\Object();
        $orderItem = new \Magento\Object(
            array('gw_id' => 1, 'gw_base_price_invoiced' => 5, 'gw_price_invoiced' => 10)
        );

        $item->setQty(2)
             ->setOrderItem($orderItem);
        $order = new \Magento\Object();

        $creditmemo->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue(array($item)));

        $creditmemo->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($order));

        $model->collect($creditmemo);

        $this->assertEquals(20, $creditmemo->getGwItemsBasePrice());
        $this->assertEquals(10, $creditmemo->getGwItemsPrice());

    }
}