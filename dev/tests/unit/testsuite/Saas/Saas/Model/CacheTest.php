<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dbaranova
 * Date: 22.03.13
 * Time: 16:15
 * To change this template use File | Settings | File Templates.
 */

class Saas_Saas_Model_CacheTest extends PHPUnit_Framework_TestCase
{
    protected $_eventManagerMock;
    protected $_cacheFrontendMock;
    protected $_cacheTypesMock;

    protected $_model;

    protected function setUp()
    {
        $this->_eventManagerMock = $this->getMockBuilder('Mage_Core_Model_Event_Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $helperMock = $this->getMock('Mage_Core_Helper_Data', array('__'), array(), '', false);
        $helperMock
            ->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0))
        ;
        $helperFactoryMock = $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false);
        $helperFactoryMock
            ->expects($this->any())
            ->method('get')
            ->with('Mage_Core_Helper_Data')
            ->will($this->returnValue($helperMock))
        ;

        $this->_initCacheTypeMocks();
        $objectManagerMock = $this->getMockForAbstractClass('Magento_ObjectManager');
        $objectManagerMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this, 'getTypeMock')));

        $this->_cacheFrontendMock = $this->getMockForAbstractClass(
            'Magento_Cache_FrontendInterface', array(), '', true, true, true, array('clean')
        );

        $frontendPoolMock = $this->getMock('Mage_Core_Model_Cache_Frontend_Pool', array(), array(), '', false);
        $frontendPoolMock
            ->expects($this->any())
            ->method('valid')
            ->will($this->onConsecutiveCalls(true, false))
        ;
        $frontendPoolMock
            ->expects($this->any())
            ->method('current')
            ->will($this->returnValue($this->_cacheFrontendMock))
        ;
        $frontendPoolMock
            ->expects($this->any())
            ->method('get')
            ->with(Mage_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID)
            ->will($this->returnValue($this->_cacheFrontendMock))
        ;

        $this->_cacheTypesMock = $this->getMock('Mage_Core_Model_Cache_Types', array(), array(), '', false);

        $configFixture = new Mage_Core_Model_Config_Base(file_get_contents(__DIR__ . '/_files/cache_types.xml'));

        $dirsMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);

        $this->_model = new Saas_Saas_Model_Cache(
            $objectManagerMock, $frontendPoolMock, $this->_cacheTypesMock, $configFixture,
            $dirsMock, $helperFactoryMock, $this->_eventManagerMock
        );
    }

    /**
     * Init necessary cache type mocks
     */
    protected function _initCacheTypeMocks()
    {
        $cacheTypes = array('Magento_Cache_Frontend_Decorator_TagScope', 'Magento_Cache_Frontend_Decorator_Bare');
        foreach ($cacheTypes as $type) {
            $this->_cacheTypeMocks[$type] = $this->getMock($type, array('clean'), array(
                $this->getMockForAbstractClass('Magento_Cache_FrontendInterface'), 'FIXTURE_TAG'
            ));
        }
    }

    /**
     * Callback for the object manager to get different cache type mocks
     *
     * @param string $type Class of the cache type
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getTypeMock($type)
    {
        return $this->_cacheTypeMocks[$type];
    }

    public function testInvalidateType()
    {
        $this->_eventManagerMock->expects($this->once())->method('dispatch')->with($this->equalTo('refresh_cache'));

        $this->_cacheFrontendMock
            ->expects($this->once())
            ->method('save')
            ->with(serialize(array('test' => 1)), Mage_Core_Model_Cache::INVALIDATED_TYPES)
        ;
        $this->_model->invalidateType('test');
    }
}