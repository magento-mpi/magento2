<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Sales\Model\Observer\Backend;

class CatalogProductQuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Observer\Backend\CatalogProductQuote
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventMock;

    protected function setUp()
    {
        $this->_quoteMock = $this->getMock('Magento\Sales\Model\Resource\Quote', array(), array(), '', false);
        $this->_observerMock = $this->getMock('Magento\Framework\Event\Observer', array(), array(), '', false);
        $this->_eventMock = $this->getMock(
            'Magento\Framework\Event',
            array('getProduct', 'getStatus', 'getProductId'),
            array(),
            '',
            false
        );
        $this->_observerMock->expects($this->any())->method('getEvent')->will($this->returnValue($this->_eventMock));
        $this->_model = new \Magento\Sales\Model\Observer\Backend\CatalogProductQuote($this->_quoteMock);
    }

    /**
     * @param int $productId
     * @param int $productStatus
     * @dataProvider statusUpdateDataProvider
     */
    public function testSaveProduct($productId, $productStatus)
    {
        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getId', 'getStatus', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_eventMock->expects($this->once())->method('getProduct')->will($this->returnValue($productMock));
        $productMock->expects($this->once())->method('getId')->will($this->returnValue($productId));
        $productMock->expects($this->once())->method('getStatus')->will($this->returnValue($productStatus));
        $this->_quoteMock->expects($this->any())->method('markQuotesRecollect');
        $this->_model->catalogProductSaveAfter($this->_observerMock);
    }

    public function statusUpdateDataProvider()
    {
        return array(array(125, 1), array(100, 0));
    }

    public function testSubtractQtyFromQuotes()
    {
        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getId', 'getStatus', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_eventMock->expects($this->once())->method('getProduct')->will($this->returnValue($productMock));
        $this->_quoteMock->expects($this->once())->method('substractProductFromQuotes')->with($productMock);
        $this->_model->subtractQtyFromQuotes($this->_observerMock);
    }
}
