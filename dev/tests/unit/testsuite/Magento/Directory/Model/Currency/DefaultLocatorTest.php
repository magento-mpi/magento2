<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Directory\Model\Currency;

class DefaultLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Directory\Model\Currency\DefaultLocator
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $backendData = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $this->_requestMock = $this->getMockForAbstractClass('Magento\App\RequestInterface',
            array($backendData), '', false, false, true, array('getParam'));
        $this->_appMock = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', array(), array(), '', false);
        $this->_model = new \Magento\Directory\Model\Currency\DefaultLocator($this->_appMock, $this->_storeManagerMock);
    }

    public function testGetDefaultCurrencyReturnDefaultStoreDefaultCurrencyIfNoStoreIsSpecified()
    {
        $this->_appMock->expects($this->once())->method('getBaseCurrencyCode')
            ->will($this->returnValue('storeCurrency'));
        $this->assertEquals('storeCurrency', $this->_model->getDefaultCurrency($this->_requestMock));
    }

    public function testGetDefaultCurrencyReturnStoreDefaultCurrency()
    {
        $this->_requestMock->expects($this->any())->method('getParam')->with('store')
            ->will($this->returnValue('someStore'));
        $storeMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->once())->method('getBaseCurrencyCode')->will($this->returnValue('storeCurrency'));
        $this->_storeManagerMock->expects($this->once())->method('getStore')->with('someStore')
            ->will($this->returnValue($storeMock));
        $this->assertEquals('storeCurrency', $this->_model->getDefaultCurrency($this->_requestMock));
    }

    public function testGetDefaultCurrencyReturnWebsiteDefaultCurrency()
    {
        $this->_requestMock->expects($this->any())->method('getParam')
            ->will($this->returnValueMap(
                array(array('store', null, ''), array('website', null, 'someWebsite')))
            );
        $websiteMock = $this->getMock('Magento\Store\Model\Website', array(), array(), '', false);
        $websiteMock->expects($this->once())->method('getBaseCurrencyCode')
            ->will($this->returnValue('websiteCurrency'));
        $this->_storeManagerMock->expects($this->once())->method('getWebsite')->with('someWebsite')
            ->will($this->returnValue($websiteMock));
        $this->assertEquals('websiteCurrency', $this->_model->getDefaultCurrency($this->_requestMock));
    }

    public function testGetDefaultCurrencyReturnGroupDefaultCurrency()
    {
        $this->_requestMock->expects($this->any())->method('getParam')
            ->will($this->returnValueMap(
                    array(array('store', null, ''), array('website', null, ''), array('group', null, 'someGroup'))
                )
            );
        $websiteMock = $this->getMock('Magento\Store\Model\Website', array(), array(), '', false);
        $websiteMock->expects($this->once())->method('getBaseCurrencyCode')
            ->will($this->returnValue('websiteCurrency'));

        $groupMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $groupMock->expects($this->once())->method('getWebsite')
            ->will($this->returnValue($websiteMock));

        $this->_storeManagerMock->expects($this->once())->method('getGroup')->with('someGroup')
            ->will($this->returnValue($groupMock));
        $this->assertEquals('websiteCurrency', $this->_model->getDefaultCurrency($this->_requestMock));
    }
}

