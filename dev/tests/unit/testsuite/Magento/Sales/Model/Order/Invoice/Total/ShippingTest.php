<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Order\Invoice\Total;

class ShippingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Retrieve new invoice collection from an array of invoices' data
     *
     * @param array $invoicesData
     * @return \Magento\Data\Collection
     */
    protected function _getInvoiceCollection(array $invoicesData)
    {
        $className = 'Magento\Sales\Model\Order\Invoice';
        $result = new \Magento\Data\Collection();
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        foreach ($invoicesData as $oneInvoiceData) {
            $arguments = $objectManagerHelper->getConstructArguments($className, array('data' => $oneInvoiceData));
            /** @var $prevInvoice \Magento\Sales\Model\Order\Invoice */
            $prevInvoice = $this->getMock($className, array('_init'), $arguments);
            $result->addItem($prevInvoice);
        }
        return $result;
    }

    /**
     * @dataProvider collectDataProvider
     * @param array $prevInvoicesData
     * @param float $orderShipping
     * @param float $invoiceShipping
     * @param float $expectedShipping
     */
    public function testCollect(array $prevInvoicesData, $orderShipping, $invoiceShipping, $expectedShipping)
    {
        /** @var $order \Magento\Sales\Model\Order|PHPUnit_Framework_MockObject_MockObject */
        $order = $this->getMock('Magento\Sales\Model\Order', array('_init', 'getInvoiceCollection'), array(), '',
            false);
        $order->setData('shipping_amount', $orderShipping);
        $order->expects($this->any())
            ->method('getInvoiceCollection')
            ->will($this->returnValue($this->_getInvoiceCollection($prevInvoicesData)))
        ;
        /** @var $invoice \Magento\Sales\Model\Order\Invoice|PHPUnit_Framework_MockObject_MockObject */
        $invoice = $this->getMock('Magento\Sales\Model\Order\Invoice', array('_init'), array(), '', false);
        $invoice->setData('shipping_amount', $invoiceShipping);
        $invoice->setOrder($order);

        $total = new \Magento\Sales\Model\Order\Invoice\Total\Shipping();
        $total->collect($invoice);

        $this->assertEquals($expectedShipping, $invoice->getShippingAmount());
    }

    public static function collectDataProvider()
    {
        return array(
            'no previous invoices' => array(
                'prevInvoicesData' => array(array()),
                'orderShipping'    => 10.00,
                'invoiceShipping'  => 5.00,
                'expectedShipping' => 10.00
            ),
            'zero shipping in previous invoices' => array(
                'prevInvoicesData' => array(array('shipping_amount' => '0.0000')),
                'orderShipping'    => 10.00,
                'invoiceShipping'  => 5.00,
                'expectedShipping' => 10.00
            ),
            'non-zero shipping in previous invoices' => array(
                'prevInvoicesData' => array(array('shipping_amount' => '10.000')),
                'orderShipping'    => 10.00,
                'invoiceShipping'  => 5.00,
                'expectedShipping' => 0
            ),
        );
    }
}
