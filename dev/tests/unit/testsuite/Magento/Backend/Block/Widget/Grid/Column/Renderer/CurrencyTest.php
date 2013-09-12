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

class Magento_Backend_Block_Widget_Grid_Column_Renderer_CurrencyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Block_Widget_Grid_Column_Renderer_Currency
     */
    protected $_blockCurrency;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_localeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_curLocatorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_columnMock;

    /**
     * @var Magento_Object
     */
    protected $_row;

    protected function setUp()
    {
        $this->_appMock = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $this->_localeMock = $this->getMock('Magento_Core_Model_LocaleInterface');
        $this->_curLocatorMock = $this->getMock(
            'Magento_Directory_Model_Currency_DefaultLocator', array(), array(), '', false
        );
        $this->_columnMock = $this->getMock(
            'Magento_Backend_Block_Widget_Grid_Column', array('getIndex'), array(), '', false
        );
        $this->_columnMock->expects($this->any())->method('getIndex')->will($this->returnValue('columnIndex'));
        $this->_row = new Magento_Object(array('columnIndex' => '10'));

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_blockCurrency = $objectManagerHelper->getObject(
            'Magento_Backend_Block_Widget_Grid_Column_Renderer_Currency',
            array(
                'locale' => $this->_localeMock,
                'app' => $this->_appMock,
                'currencyLocator' => $this->_curLocatorMock,
                'urlBuilder' => $this->getMock(
                    'Magento_Backend_Model_Url', array(), array(), '', false
                )
            )
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
        unset($this->_blockCurrency);
    }

    public function testRenderWithDefaultCurrency()
    {
        $currencyMock = $this->getMock('Magento_Directory_Model_Currency', array(), array(), '', false);
        $currencyMock->expects($this->once())->method('getRate')->with('defaultCurrency')
            ->will($this->returnValue(1.5));

        $storeMock = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $storeMock->expects($this->once())->method('getBaseCurrency')->will($this->returnValue($currencyMock));

        $this->_appMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->_curLocatorMock->expects($this->any())->method('getDefaultCurrency')->will($this->returnValue(
            'defaultCurrency'
        ));

        $currLocaleMock = $this->getMock('Zend_Currency', array(), array(), '', false);
        $currLocaleMock->expects($this->once())->method('toCurrency')->with(15.0000)->will($this->returnValue('15USD'));
        $this->_localeMock->expects($this->once())->method('currency')->with('defaultCurrency')
            ->will($this->returnValue($currLocaleMock));

        $this->assertEquals('15USD', $this->_blockCurrency->render($this->_row));
    }
}
