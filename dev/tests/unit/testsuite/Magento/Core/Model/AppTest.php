<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_App
 */
class Magento_Core_Model_AppTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_App
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontControllerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dbUpdaterMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false, false);
        $this->_frontControllerMock = $this->getMock('Magento_Core_Controller_Varien_Front',
            array(), array(), '', false, false);
        $this->_cacheMock = $this->getMock('Magento_Core_Model_CacheInterface', array(), array(), '', false, false);
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false, false);
        $this->_dbUpdaterMock = $this->getMock('Magento_Core_Model_Db_UpdaterInterface',
            array(), array(), '', false, false);
        $this->_storeManagerMock = $this->getMock('Magento_Core_Model_StoreManagerInterface');
        $this->_eventManagerMock = $this->getMock('Magento_Core_Model_Event_Manager',
            array(), array(), '', false, false);
        $this->_appStateMock = $this->getMock('Magento_Core_Model_App_State', array(), array(), '', false, false);
        $this->_model = new Magento_Core_Model_App(
            $this->_configMock,
            $this->_frontControllerMock,
            $this->_cacheMock,
            $this->_objectManagerMock,
            $this->_dbUpdaterMock,
            $this->_storeManagerMock,
            $this->_eventManagerMock,
            $this->_appStateMock,
            $this->getMock('Magento_Core_Model_Config_Scope', array(), array(), '', false)
        );
    }

    protected function tearDown()
    {
        unset($this->_configMock);
        unset($this->_frontControllerMock);
        unset($this->_cacheMock);
        unset($this->_objectManagerMock);
        unset($this->_dbUpdaterMock);
        unset($this->_storeManagerMock);
        unset($this->_eventManagerMock);
        unset($this->_appStateMock);
        unset($this->_model);
    }

    public function testGetSafeStore()
    {
        $storeId = 'test';
        $this->_storeManagerMock->expects($this->once())
            ->method('getSafeStore')
            ->with($this->equalTo($storeId))
            ->will($this->returnValue('proxy_result'));
        $this->assertEquals('proxy_result', $this->_model->getSafeStore($storeId));
    }

    public function testSetIsSingleStoreModeAllowed()
    {
        $value = true;
        $this->_storeManagerMock->expects($this->once())
            ->method('setIsSingleStoreModeAllowed')
            ->with($this->equalTo($value));
        $this->_model->setIsSingleStoreModeAllowed($value);
    }

    public function testHasSingleStore()
    {
        $this->_storeManagerMock->expects($this->once())
            ->method('hasSingleStore')
            ->will($this->returnValue('proxy_result'));
        $this->assertEquals('proxy_result', $this->_model->hasSingleStore());
    }

    public function testIsSingleStoreMode()
    {
        $this->_storeManagerMock->expects($this->once())
            ->method('isSingleStoreMode')
            ->will($this->returnValue('proxy_result'));
        $this->assertEquals('proxy_result', $this->_model->isSingleStoreMode());
    }

    public function testThrowStoreException()
    {
        $this->_storeManagerMock->expects($this->once())
            ->method('throwStoreException');
        $this->_model->throwStoreException();
    }

    public function testGetStore()
    {
        $storeId = 'some_value';
        $this->_storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($this->equalTo($storeId))
            ->will($this->returnValue('proxy_result'));
        $this->assertEquals('proxy_result', $this->_model->getStore($storeId));
    }

    public function testGetStores()
    {
        $withDefault = true;
        $codeKey = true;
        $this->_storeManagerMock->expects($this->once())
            ->method('getStores')
            ->with($this->equalTo($withDefault),
                   $this->equalTo($codeKey))
            ->will($this->returnValue('proxy_result'));
        $this->assertEquals('proxy_result', $this->_model->getStores($withDefault, $codeKey));
    }

    public function testGetWebsite()
    {
        $websiteId = 'some_value';
        $this->_storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->with($this->equalTo($websiteId))
            ->will($this->returnValue('proxy_result'));
        $this->assertEquals('proxy_result', $this->_model->getWebsite($websiteId));
    }

    public function testGetWebsites()
    {
        $withDefault = true;
        $codeKey = true;
        $this->_storeManagerMock->expects($this->once())
            ->method('getWebsites')
            ->with($this->equalTo($withDefault),
                   $this->equalTo($codeKey))
            ->will($this->returnValue('proxy_result'));
        $this->assertEquals('proxy_result', $this->_model->getWebsites($withDefault, $codeKey));
    }

    public function testReinitStores()
    {
        $this->_storeManagerMock->expects($this->once())
            ->method('reinitStores');
        $this->_model->reinitStores();
    }

    public function testSetCurrentStore()
    {
        $store = 'Test';
        $this->_storeManagerMock->expects($this->once())
            ->method('setCurrentStore')
            ->with($this->equalTo($store));
        $this->_model->setCurrentStore($store);
    }

    public function testGetDefaultStoreView()
    {
        $this->_storeManagerMock->expects($this->once())
            ->method('getDefaultStoreView')
            ->will($this->returnValue('proxy_result'));
        $this->assertEquals('proxy_result', $this->_model->getDefaultStoreView());
    }

    public function testGetGroup()
    {
        $groupId = 'test';
        $this->_storeManagerMock->expects($this->once())
            ->method('getGroup')
            ->will($this->returnValue('proxy_result'))
            ->with($this->equalTo($groupId));
        $this->assertEquals('proxy_result', $this->_model->getGroup($groupId));
    }

    public function testGetGroups()
    {
        $withDefault = true;
        $codeKey = true;
        $this->_storeManagerMock->expects($this->once())
            ->method('getGroups')
            ->with($this->equalTo($withDefault),
            $this->equalTo($codeKey))
            ->will($this->returnValue('proxy_result'));
        $this->assertEquals('proxy_result', $this->_model->getGroups($withDefault, $codeKey));
    }

    public function testClearWebsiteCache()
    {
        $websiteId = 'Test';
        $this->_storeManagerMock->expects($this->once())
            ->method('clearWebsiteCache')
            ->with($this->equalTo($websiteId));
        $this->_model->clearWebsiteCache($websiteId);
    }

    public function testGetAnyStoreView()
    {
        $this->_storeManagerMock->expects($this->once())
            ->method('getAnyStoreView')
            ->will($this->returnValue('proxy_result'));
        $this->assertEquals('proxy_result', $this->_model->getAnyStoreView());
    }
}
