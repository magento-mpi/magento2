<?php
/**
 * Test class for \Magento\Store\Model\StorageFactory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Model;

class StorageFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\Model\StorageFactory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sidResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    /**
     * @var \Magento\Stdlib\Cookie
     */
    protected $_cookie;

    /**
     * @var \Magento\App\Http\Context
     */
    protected $_httpContext;

    /**
     * @var string
     */
    protected $_defaultStorage = 'Magento\Store\Model\Storage\DefaultStorage';

    /**
     * @var string
     */
    protected $_dbStorage = 'Magento\Store\Model\Storage\Db';

    /**
     * @var array
     */
    protected $_arguments = array();

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeConfig;

    protected function setUp()
    {
        $this->_arguments = array('test' => 'argument', 'scopeCode' => '', 'scopeType' => '');
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_eventManagerMock = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $this->_logMock = $this->getMock('Magento\Logger', array(), array(), '', false);
        $this->_sidResolverMock = $this->getMock('\Magento\Session\SidResolverInterface', array(), array(), '', false);
        $this->_appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->_storage = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->_cookie = $this->getMock('Magento\Stdlib\Cookie', array(), array(), '', false);
        $this->_httpContext = $this->getMock('Magento\App\Http\Context', array(), array(), '', false);
        $this->_scopeConfig = $this->getMock('Magento\App\Config\ScopeConfigInterface');

        $this->_model = new \Magento\Store\Model\StorageFactory(
            $this->_objectManagerMock,
            $this->_eventManagerMock,
            $this->_logMock,
            $this->_sidResolverMock,
            $this->_appStateMock,
            $this->_cookie,
            $this->_httpContext,
            $this->_scopeConfig,
            $this->_defaultStorage,
            $this->_dbStorage
        );
    }

    public function testGetInNotInstalledModeWithInternalCache()
    {
        $this->_appStateMock->expects($this->exactly(2))->method('isInstalled')->will($this->returnValue(false));

        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $this->_defaultStorage
        )->will(
            $this->returnValue($this->_storage)
        );

        $this->_eventManagerMock->expects($this->never())->method('dispatch');
        $this->_logMock->expects($this->never())->method('initForStore');
        $this->_sidResolverMock->expects($this->never())->method('setUseSessionInUrl');

        /** test create instance */
        $this->assertEquals($this->_storage, $this->_model->get($this->_arguments));

        /** test read instance from internal cache */
        $this->assertEquals($this->_storage, $this->_model->get($this->_arguments));
    }

    public function testGetInstalledModeWithInternalCache()
    {
        $this->_appStateMock->expects($this->exactly(2))->method('isInstalled')->will($this->returnValue(true));

        $store = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);

        $this->_storage->expects($this->exactly(3))->method('getStore')->will($this->returnValue($store));

        $this->_scopeConfig->expects(
            $this->at(0)
        )->method(
            'isSetFlag'
        )->with(
            \Magento\Session\SidResolver::XML_PATH_USE_FRONTEND_SID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will(
            $this->returnValue(true)
        );

        $this->_scopeConfig->expects(
            $this->at(1)
        )->method(
            'isSetFlag'
        )->with(
            'dev/log/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will(
            $this->returnValue(true)
        );

        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $this->_dbStorage
        )->will(
            $this->returnValue($this->_storage)
        );

        $this->_eventManagerMock->expects(
            $this->once()
        )->method(
            'dispatch'
        )->with(
            'core_app_init_current_store_after'
        );

        $this->_logMock->expects($this->once())->method('unsetLoggers');
        $this->_logMock->expects($this->exactly(2))->method('addStreamLog');

        $this->_sidResolverMock->expects($this->once())->method('setUseSessionInUrl')->with(true);

        /** test create instance */
        $this->assertEquals($this->_storage, $this->_model->get($this->_arguments));

        /** test read instance from internal cache */
        $this->assertEquals($this->_storage, $this->_model->get($this->_arguments));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetWithInvalidStorageClassName()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));

        $invalidObject = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);

        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $this->_dbStorage
        )->will(
            $this->returnValue($invalidObject)
        );

        $this->_eventManagerMock->expects($this->never())->method('dispatch');
        $this->_logMock->expects($this->never())->method('initForStore');
        $this->_sidResolverMock->expects($this->never())->method('setUseSessionInUrl');

        /** test create instance */
        $this->assertEquals($this->_storage, $this->_model->get($this->_arguments));

        /** test read instance from internal cache */
        $this->assertEquals($this->_storage, $this->_model->get($this->_arguments));
    }

    public function testGetWishStoresReinit()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));

        $website = $this->getMock('Magento\Store\Model\Website', array(), array(), '', false);
        $website->expects($this->once())->method('getCode')->will($this->returnValue('code'));
        $website->expects($this->any())->method('getDefaultGroupId')->will($this->returnValue(1));

        $group = $this->getMock(
            'Magento\Store\Model\Group',
            array('getDefaultStoreId', '__sleep', '__wakeup'),
            array(),
            '',
            false
        );
        $group->expects($this->any())->method('getDefaultStoreId')->will($this->returnValue(1));

        $store = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $store->expects($this->exactly(3))->method('getCode')->will($this->returnValue(1));
        $store->expects($this->once())->method('getId')->will($this->returnValue(1));
        $store->expects($this->once())->method('getIsActive')->will($this->returnValue(true));
        $store->expects($this->any())->method('getConfig')->will($this->returnValue(1));

        $storage = $this->getMock('Magento\Store\Model\Storage\Db', array(), array(), '', false);
        $storage->expects($this->any())->method('getWebsite')->will($this->returnValue($website));
        $storage->expects($this->any())->method('getWebsites')->will($this->returnValue(array('code' => $website)));
        $storage->expects($this->any())->method('getGroups')->will($this->returnValue(array('1' => $group)));
        $storage->expects($this->any())->method('getStores')->will($this->returnValue(array('1' => $store)));
        $storage->expects($this->any())->method('setCurrentStore')->with('1');
        $storage->expects($this->any())->method('getCurrentStore')->will($this->returnValue(1));
        $storage->expects($this->any())->method('getStore')->will($this->returnValue($store));

        $this->_objectManagerMock->expects($this->once())->method('create')->will($this->returnValue($storage));

        $this->_cookie->expects($this->any())->method('get')->will($this->returnValue(1));

        $this->assertEquals($storage, $this->_model->get($this->_arguments));
    }
}
