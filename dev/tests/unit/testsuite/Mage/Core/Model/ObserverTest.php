<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    public function testCleanCache()
    {
        $cacheBackendMock = $this->getMockForAbstractClass('Zend_Cache_Backend_Interface');
        $cacheBackendMock
            ->expects($this->once())
            ->method('clean')
            ->with(Zend_Cache::CLEANING_MODE_OLD, array())
        ;

        $cacheFrontendMock = $this->getMockForAbstractClass('Magento_Cache_FrontendInterface');
        $cacheFrontendMock
            ->expects($this->once())
            ->method('getBackend')
            ->will($this->returnValue($cacheBackendMock))
        ;

        $frontendPoolMock = $this->getMock('Mage_Core_Model_Cache_Frontend_Pool', array(), array(), '', false);
        $frontendPoolMock
            ->expects($this->any())
            ->method('valid')
            ->will($this->onConsecutiveCalls(true, false))
        ;
        $frontendPoolMock
            ->expects($this->any())
            ->method('current')
            ->will($this->returnValue($cacheFrontendMock))
        ;

        $cronScheduleMock = $this->getMock('Mage_Cron_Model_Schedule', array(), array(), '', false);

        $object = new Mage_Core_Model_Observer($frontendPoolMock);
        $object->cleanCache($cronScheduleMock);
    }
}
