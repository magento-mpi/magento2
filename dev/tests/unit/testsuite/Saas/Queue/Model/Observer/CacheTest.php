<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Queue_Model_Observer_CacheTest extends PHPUnit_Framework_TestCase
{
    /*
     * @var PHPUnit_Framework_MockObject_MockObject $_cache
     */
    protected $_cache;

    /**
     * @var Saas_Queue_Model_Observer_Cache $_jobCache
     */
    protected $_jobCache;

    public function setUp()
    {
        $this->_cache = $this->getMockBuilder('Mage_Core_Model_Cache')->disableOriginalConstructor()->getMock();
        $this->_jobCache = new Saas_Queue_Model_Observer_Cache($this->_cache);
    }

    public function testUseInEmailNotification()
    {
        $this->assertFalse($this->_jobCache->useInEmailNotification());
    }

    /**
     * @param array $types
     * @dataProvider cacheTypesDataProvider
     */
    public function testRefreshCacheWithParams($types)
    {
        $event = new Magento_Event(array('cache_types' => $types));
        $observer = new Magento_Event_Observer();
        $observer->setEvent($event);

        $this->_cache->expects($this->exactly(count($types)))->method('cleanType');
        $this->_jobCache->processRefreshCache($observer);
    }

    /**
     * @param array $types
     * @dataProvider cacheTypesDataProvider
     */
    public function testRefreshCacheWithoutParams($types)
    {
        $event = new Magento_Event(array('cache_types' => array()));
        $observer = new Magento_Event_Observer();
        $observer->setEvent($event);

        $this->_cache
            ->expects($this->once())
            ->method('getTypes')
            ->will($this->returnValue($types));

        $this->_cache->expects($this->exactly(count($types)))->method('cleanType');
        $this->_jobCache->processRefreshCache($observer);
    }

    /**
     * @param array $types
     * @dataProvider cacheTypesDataProvider
     */
    public function testRefreshAllCache($types)
    {
        $this->_cache
            ->expects($this->once())
            ->method('getTypes')
            ->will($this->returnValue($types));
        $this->_cache->expects($this->exactly(count($types)))->method('cleanType');
        $this->_jobCache->processRefreshAllCache();
    }

    public function cacheTypesDataProvider()
    {
        return array(
            array(array('test')),
            array(array('', 'test2', '1234'))
        );
    }
}
