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

namespace Magento\GiftWrapping\Model\Invoice\Tax;

/**
 * Test class for \Magento\GiftWrapping\Model\Invoice\Tax\Giftwrapping
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoiceItemTaxWrapping()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $model = $objectHelper->getObject(
            'Magento\GiftWrapping\Model\Total\Invoice\Tax\Giftwrapping',
            array()
        );

        $invoice = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getAllItems',
                    'getOrder',
                    '__wakeup',
                    'isLast',
                    'setGwItemsBaseTaxAmount',
                    'setGwItemsTaxAmount'
                )
            )
            ->getMock();

        $item = new \Magento\Object();
        $orderItem = new \Magento\Object(
            array('gw_id' => 1, 'gw_base_tax_amount' => 5, 'gw_tax_amount' => 10)
        );

        $item->setQty(2)
             ->setOrderItem($orderItem);
        $order = new \Magento\Object();

        $invoice->expects($this->any())
            ->method('getAllItems')
            ->will($this->returnValue(array($item)));
        $invoice->expects($this->any())
            ->method('isLast')
            ->will($this->returnValue(true));
        $invoice->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($order));
        $invoice->expects($this->once())
            ->method('setGwItemsBaseTaxAmount')
            ->with(10);
        $invoice->expects($this->once())
            ->method('setGwItemsTaxAmount')
            ->with(20);

        $model->collect($invoice);

    }
}