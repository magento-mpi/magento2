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

namespace Magento\GiftWrapping\Model\Creditmemo\Tax;

/**
 * Test class for \Magento\GiftWrapping\Model\Creditmemo\Tax\Giftwrapping
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    public function testCreditmemoItemTaxWrapping()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $model = $objectHelper->getObject(
            'Magento\GiftWrapping\Model\Total\Creditmemo\Tax\Giftwrapping',
            array()
        );

        $creditmemo = $this->getMockBuilder('Magento\Sales\Model\Order\Creditmemo')
            ->disableOriginalConstructor()
            ->setMethods(array('getAllItems', 'getOrder', 'setGwItemsBaseTaxAmount', 'setGwItemsTaxAmount',
                'getGwItemsBaseTaxAmount', 'getGwBaseTaxAmount', 'getGwCardBaseTaxAmount', 'getGwItemsTaxAmount',
                'getGwTaxAmount', 'getGwCardTaxAmount', 'setBaseTaxAmount', 'getBaseTaxAmount', 'setTaxAmount',
                'getTaxAmount', 'setBaseGrandTotal', 'getBaseGrandTotal', 'setGrandTotal', 'getGrandTotal',
                'setBaseCustomerBalanceReturnMax', 'getBaseCustomerBalanceReturnMax', 'setCustomerBalanceReturnMax',
                'getCustomerBalanceReturnMax'))
            ->getMock();

        $item = new \Magento\Object();
        $orderItem = new \Magento\Object(
            array('gw_id' => 1, 'gw_base_tax_amount_invoiced' => 5, 'gw_tax_amount_invoiced' => 10)
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
        $creditmemo->expects($this->once())
            ->method('setGwItemsBaseTaxAmount')
            ->with(10);
        $creditmemo->expects($this->once())
            ->method('setGwItemsTaxAmount')
            ->with(20);

        $model->collect($creditmemo);

    }
}