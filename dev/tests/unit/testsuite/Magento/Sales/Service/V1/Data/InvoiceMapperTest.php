<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class InvoiceMapperTest
 */
class InvoiceMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InvoiceMapper
     */
    protected $invoiceMapper;

    /**
     * @var InvoiceBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceBuilderMock;

    /**
     * @var InvoiceItemMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceItemMapperMock;

    /**
     * @var \Magento\Sales\Model\Order\Invoice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceMock;

    /**
     * @var \Magento\Sales\Model\Order\Invoice\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceItemMock;

    /**
     * SetUp
     *
     * @return void
     */
    protected function setUp()
    {
        $this->invoiceBuilderMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\InvoiceBuilder',
            ['populateWithArray', 'setItems', 'create'],
            [],
            '',
            false
        );
        $this->invoiceItemMapperMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\InvoiceItemMapper',
            ['extractDto'],
            [],
            '',
            false
        );
        $this->invoiceMock = $this->getMock(
            'Magento\Sales\Model\Order\Invoice',
            ['getAllItems', 'getData', '__wakeup'],
            [],
            '',
            false
        );
        $this->invoiceItemMock = $this->getMock(
            'Magento\Sales\Model\Order\Invoice\Item',
            [],
            [],
            '',
            false
        );
        $this->invoiceMapper = new InvoiceMapper(
            $this->invoiceBuilderMock,
            $this->invoiceItemMapperMock
        );
    }

    /**
     * Run invoice mapper test
     *
     * @return void
     */
    public function testInvoke()
    {
        $this->invoiceMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['field-1' => 'value-1']));
        $this->invoiceMock->expects($this->once())
            ->method('getAllItems')
            ->will($this->returnValue([$this->invoiceItemMock]));
        $this->invoiceBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($this->equalTo(['field-1' => 'value-1']))
            ->will($this->returnSelf());
        $this->invoiceItemMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($this->equalTo($this->invoiceItemMock))
            ->will($this->returnValue('item-1'));
        $this->invoiceBuilderMock->expects($this->once())
            ->method('setItems')
            ->with($this->equalTo(['item-1']))
            ->will($this->returnSelf());
        $this->invoiceBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('data-object-with-invoice'));
        $this->assertEquals('data-object-with-invoice', $this->invoiceMapper->extractDto($this->invoiceMock));
    }
}
