<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Saas_Model_CacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    protected function setUp()
    {
        $this->_eventManagerMock = $this->getMockBuilder('Mage_Core_Model_Event_Manager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testInvalidateType()
    {
        $this->_eventManagerMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('application_process_refresh_cache'));
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $objectManager->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Mage_Core_Model_Event_Manager'))
            ->will($this->returnValue($this->_eventManagerMock));

        $modelCacheMock = $this->getMockBuilder('Saas_Saas_Model_Cache')
            ->setConstructorArgs(array(
                $objectManager,
                $this->getMock('Mage_Core_Model_Cache_Frontend_Pool', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Cache_Types', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_ConfigInterface', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false)
            ))
            ->setMethods(array('_callOriginInvalidateType'))
            ->getMock();

        $modelCacheMock->expects($this->once())->method('_callOriginInvalidateType')->will($this->returnSelf());
        $this->assertEquals($modelCacheMock, $modelCacheMock->invalidateType('test'));
    }
}
