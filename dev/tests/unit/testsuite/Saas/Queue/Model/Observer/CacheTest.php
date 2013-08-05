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
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheTypeListMock;

    /**
     * @var Saas_Queue_Model_Observer_Cache
     */
    protected $_jobCache;

    public function setUp()
    {
        $this->_cacheTypeListMock = $this->getMock('Mage_Core_Model_Cache_TypeListInterface');
        $this->_jobCache = new Saas_Queue_Model_Observer_Cache($this->_cacheTypeListMock);
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

        $this->_cacheTypeListMock->expects($this->exactly(count($types)))->method('cleanType');
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

        $this->_cacheTypeListMock
            ->expects($this->once())
            ->method('getTypes')
            ->will($this->returnValue($types));

        $this->_cacheTypeListMock->expects($this->exactly(count($types)))->method('cleanType');
        $this->_jobCache->processRefreshCache($observer);
    }

    /**
     * @param array $types
     * @dataProvider cacheTypesDataProvider
     */
    public function testRefreshAllCache($types)
    {
        $this->_cacheTypeListMock
            ->expects($this->once())
            ->method('getTypes')
            ->will($this->returnValue($types));
        $this->_cacheTypeListMock->expects($this->exactly(count($types)))->method('cleanType');
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
