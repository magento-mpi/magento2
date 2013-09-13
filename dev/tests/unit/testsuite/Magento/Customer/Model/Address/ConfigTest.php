<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Model_Address_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_addressHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * @var Magento_Customer_Model_Address_Config
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_cacheId = 'cache_id';

    public function setUp()
    {
        $this->_storeMock = $this->getMock('Magento_Core_Model_Store', array('getId', 'getConfig'), array(), '', false);

        $this->_readerMock = $this->getMock(
            'Magento_Customer_Model_Address_Config_Reader',
            array(), array(), '', false
        );
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $this->_storeManagerMock = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);
        $this->_storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($this->_storeMock));

        $this->_addressHelperMock = $this->getMock('Magento_Customer_Helper_Address', array(), array(), '', false);

        $this->_model = new Magento_Customer_Model_Address_Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            $this->_storeManagerMock,
            $this->_addressHelperMock,
            $this->_cacheId
        );
    }

    public function testGetStore()
    {
        $this->assertEquals($this->_storeMock, $this->_model->getStore());
    }

    public function testSetStore()
    {
        $this->_model->setStore($this->_storeMock);

        //no call to $_storeManagerMock's method
        $this->assertEquals($this->_storeMock, $this->_model->getStore());
    }

    public function testGetFormats()
    {
        $fixtureConfigData = require __DIR__ . '/Config/_files/formats_merged.php';

        $this->_storeMock->expects($this->once())
            ->method('getId');

        $this->_storeMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue('someValue'));

        $this->_configScopeMock
            ->expects($this->any())
            ->method('getCurrentScope')
            ->will($this->returnValue('global'));

        $this->_cacheMock
            ->expects($this->once())
            ->method('get')
            ->with('global', $this->_cacheId)
            ->will($this->returnValue(false))
        ;
        $this->_cacheMock
            ->expects($this->once())
            ->method('put')
            ->with($fixtureConfigData, 'global', $this->_cacheId)
        ;
        $this->_readerMock
            ->expects($this->once())
            ->method('read')
            ->with('global')
            ->will($this->returnValue($fixtureConfigData))
        ;

        $rendererMock = $this->getMock('Magento_Object');

        $this->_addressHelperMock
            ->expects($this->any())
            ->method('getRenderer')
            ->will($this->returnValue($rendererMock));

        $firstExpected = new Magento_Object();
        $firstExpected->setCode('format_one')
            ->setTitle('format_one_title')
            ->setDefaultFormat('someValue')
            ->setEscapeHtml(false)
            ->setRenderer(null);

        $secondExpected = new Magento_Object();
        $secondExpected->setCode('format_two')
            ->setTitle('format_two_title')
            ->setDefaultFormat('someValue')
            ->setEscapeHtml(true)
            ->setRenderer(null);
        $expectedResult = array($firstExpected, $secondExpected);

        $this->assertEquals($expectedResult, $this->_model->getFormats());
    }
}
