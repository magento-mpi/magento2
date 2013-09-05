<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Cache_Type_FrontendPoolTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Cache_Type_FrontendPool
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Cache_Frontend_Pool|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cachePool;

    public function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager', array(), array(), '', false);
        $this->_cachePool = $this->getMock('Magento_Core_Model_Cache_Frontend_Pool', array(), array(), '', false);
        $this->_model = new Magento_Core_Model_Cache_Type_FrontendPool($this->_objectManager, $this->_cachePool);
    }

    public function testGet()
    {
        $instanceMock = $this->getMock('Magento\Cache\FrontendInterface');
        $this->_cachePool->expects($this->once())
            ->method('get')
            ->with('cache_type')
            ->will($this->returnValue($instanceMock));

        $accessMock = $this->getMock('Magento_Core_Model_Cache_Type_AccessProxy', array(), array(), '', false);
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento_Core_Model_Cache_Type_AccessProxy',
                array('frontend' => $instanceMock, 'identifier' => 'cache_type'))
            ->will($this->returnValue($accessMock));

        $instance = $this->_model->get('cache_type');
        $this->assertSame($accessMock, $instance);

        // And must be cached
        $instance = $this->_model->get('cache_type');
        $this->assertSame($accessMock, $instance);
    }

    public function testGetFallbackToDefaultId()
    {
        /**
         * Setup cache pool to have knowledge only about default cache instance. Also check appropriate sequence
         * of calls.
         */
        $defaultInstance = $this->getMock('Magento\Cache\FrontendInterface');
        $this->_cachePool->expects($this->at(0))
            ->method('get')
            ->with('cache_type')
            ->will($this->returnValue(null));
        $this->_cachePool->expects($this->at(1))
            ->method('get')
            ->with(Magento_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID)
            ->will($this->returnValue($defaultInstance));

        $this->_cachePool->expects($this->at(2))
            ->method('get')
            ->with('another_cache_type')
            ->will($this->returnValue(null));
        $this->_cachePool->expects($this->at(3))
            ->method('get')
            ->with(Magento_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID)
            ->will($this->returnValue($defaultInstance));

        /**
         * Setup object manager to create new access proxies. We expect two calls.
         */
        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with('Magento_Core_Model_Cache_Type_AccessProxy',
                array('frontend' => $defaultInstance, 'identifier' => 'cache_type'))
            ->will($this->returnValue(
                $this->getMock('Magento_Core_Model_Cache_Type_AccessProxy', array(), array(), '', false)
        ));
        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with('Magento_Core_Model_Cache_Type_AccessProxy',
                array('frontend' => $defaultInstance, 'identifier' => 'another_cache_type'))
            ->will($this->returnValue(
                $this->getMock('Magento_Core_Model_Cache_Type_AccessProxy', array(), array(), '', false)
        ));

        $cacheInstance = $this->_model->get('cache_type');
        $anotherInstance = $this->_model->get('another_cache_type');
        $this->assertNotSame($cacheInstance, $anotherInstance,
            'Different cache instances must be returned for different identifiers');
    }
}
