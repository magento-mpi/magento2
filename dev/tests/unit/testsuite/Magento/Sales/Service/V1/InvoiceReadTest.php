<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class InvoiceReadTest
 */
class InvoiceReadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\InvoiceGet|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceGetMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\InvoiceList|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceListMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\InvoiceCommentsList|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceCommentsListMock;

    /**
     * @var \Magento\Sales\Service\V1\InvoiceRead
     */
    protected $invoiceRead;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->invoiceGetMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\InvoiceGet',
            ['invoke'],
            [],
            '',
            false
        );
        $this->invoiceListMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\InvoiceList',
            ['invoke'],
            [],
            '',
            false
        );
        $this->invoiceCommentsListMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\InvoiceCommentsList',
            ['invoke'],
            [],
            '',
            false
        );

        $this->invoiceRead = new InvoiceRead(
            $this->invoiceGetMock,
            $this->invoiceListMock,
            $this->invoiceCommentsListMock
        );
    }

    /**
     * test invoice get
     */
    public function testGet()
    {
        $this->invoiceGetMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue('invoice-do'));
        $this->assertEquals('invoice-do', $this->invoiceRead->get(1));
    }

    /**
     * test invoice list
     */
    public function testSearch()
    {
        $searchCriteria = $this->getMock('Magento\Framework\Api\SearchCriteria', [], [], '', false);
        $this->invoiceListMock->expects($this->once())
            ->method('invoke')
            ->with($searchCriteria)
            ->will($this->returnValue('search_result'));
        $this->assertEquals('search_result', $this->invoiceRead->search($searchCriteria));
    }

    /**
     * test invoice comments list
     */
    public function testCommentsList()
    {
        $this->invoiceCommentsListMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue('search_result'));
        $this->assertEquals('search_result', $this->invoiceRead->commentsList(1));
    }
}
