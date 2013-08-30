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

class Magento_Directory_Model_Currency_DefaultLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Directory_Model_Currency_DefaultLocator
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento_Core_Controller_Request_Http');
        $this->_appMock = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $this->_model = new Magento_Directory_Model_Currency_DefaultLocator($this->_appMock);
    }

    public function testGetDefaultCurrencyReturnDefaultStoreDefaultCurrencyIfNoStoreIsSpecified()
    {
        $storeMock = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $storeMock->expects($this->once())->method('getBaseCurrencyCode')->will($this->returnValue('storeCurrency'));
        $this->_appMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $this->assertEquals('storeCurrency', $this->_model->getDefaultCurrency($this->_requestMock));
    }

    public function testGetDefaultCurrencyReturnStoreDefaultCurrency()
    {
        $this->_requestMock->expects($this->any())->method('getParam')->with('store')
            ->will($this->returnValue('someStore'));
        $storeMock = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $storeMock->expects($this->once())->method('getBaseCurrencyCode')->will($this->returnValue('storeCurrency'));
        $this->_appMock->expects($this->once())->method('getStore')->with('someStore')
            ->will($this->returnValue($storeMock));
        $this->assertEquals('storeCurrency', $this->_model->getDefaultCurrency($this->_requestMock));
    }

    public function testGetDefaultCurrencyReturnWebsiteDefaultCurrency()
    {
        $this->_requestMock->expects($this->any())->method('getParam')
            ->will($this->returnValueMap(
                array(array('store', null, ''), array('website', null, 'someWebsite')))
            );
        $websiteMock = $this->getMock('Magento_Core_Model_Website', array(), array(), '', false);
        $websiteMock->expects($this->once())->method('getBaseCurrencyCode')
            ->will($this->returnValue('websiteCurrency'));
        $this->_appMock->expects($this->once())->method('getWebsite')->with('someWebsite')
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
        $websiteMock = $this->getMock('Magento_Core_Model_Website', array(), array(), '', false);
        $websiteMock->expects($this->once())->method('getBaseCurrencyCode')
            ->will($this->returnValue('websiteCurrency'));

        $groupMock = $this->getMock('Magento_Core_Model_Store_Group', array(), array(), '', false);
        $groupMock->expects($this->once())->method('getWebsite')
            ->will($this->returnValue($websiteMock));

        $this->_appMock->expects($this->once())->method('getGroup')->with('someGroup')
            ->will($this->returnValue($groupMock));
        $this->assertEquals('websiteCurrency', $this->_model->getDefaultCurrency($this->_requestMock));
    }
}

