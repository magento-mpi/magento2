<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\Widget\Grid\Column\Renderer\Currency
     */
    protected $_blockCurrency;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_localeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_curLocatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_columnMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \Magento\Object
     */
    protected $_row;

    protected function setUp()
    {
        $this->_contextMock = $this->getMock('Magento\Backend\Block\Context', array(), array(), '', false);
        $this->_appMock = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManagerInterface');
        $this->_localeMock = $this->getMock('Magento\Core\Model\LocaleInterface');
        $this->_requestMock = $this->getMock('Magento\App\RequestInterface');

        $this->_curLocatorMock = $this->getMock(
            'Magento\Directory\Model\Currency\DefaultLocator', array(), array(), '', false
        );
        $this->_columnMock = $this->getMock(
            'Magento\Backend\Block\Widget\Grid\Column', array('getIndex'), array(), '', false
        );
        $this->_columnMock->expects($this->any())
            ->method('getIndex')
            ->will($this->returnValue('columnIndex'));

        $this->_contextMock->expects($this->any())
            ->method('getApp')
            ->will($this->returnValue($this->_appMock));

        $this->_contextMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->_requestMock));

        $this->_row = new \Magento\Object(array('columnIndex' => '10'));

        $this->_blockCurrency = new \Magento\Backend\Block\Widget\Grid\Column\Renderer\Currency(
            $this->_contextMock,
            $this->_storeManagerMock,
            $this->_localeMock,
            $this->_curLocatorMock
        );

        $this->_blockCurrency->setColumn($this->_columnMock);
    }

    protected function tearDown()
    {
        unset($this->_appMock);
        unset($this->_localeMock);
        unset($this->_curLocatorMock);
        unset($this->_columnMock);
        unset($this->_row);
        unset($this->_storeManagerMock);
        unset($this->_requestMock);
        unset($this->_contextMock);
        unset($this->_blockCurrency);
    }

    /**
     * @covers \Magento\Backend\Block\Widget\Grid\Column\Renderer\Currency::render
     */
    public function testRenderWithDefaultCurrency()
    {
        $currencyMock = $this->getMock('Magento\Directory\Model\Currency', array(), array(), '', false);
        $currencyMock->expects($this->once())->method('getRate')->with('defaultCurrency')
            ->will($this->returnValue(1.5));

        $storeMock = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->once())->method('getBaseCurrency')->will($this->returnValue($currencyMock));

        $this->_storeManagerMock->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($storeMock));

        $this->_curLocatorMock->expects($this->any())
            ->method('getDefaultCurrency')
            ->with($this->_requestMock)
            ->will($this->returnValue('defaultCurrency'));

        $currLocaleMock = $this->getMock('Zend_Currency', array(), array(), '', false);
        $currLocaleMock->expects($this->once())->method('toCurrency')->with(15.0000)->will($this->returnValue('15USD'));
        $this->_localeMock->expects($this->once())->method('currency')->with('defaultCurrency')
            ->will($this->returnValue($currLocaleMock));

        $this->assertEquals('15USD', $this->_blockCurrency->render($this->_row));
    }
}
