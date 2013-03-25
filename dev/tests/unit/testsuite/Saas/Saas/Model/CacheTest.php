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
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManagerMock;

    /**
     * @var Saas_Saas_Model_Cache
     */
    protected $_modelCacheMock;

    protected function setUp()
    {
        $this->_eventManagerMock = $this->getMockBuilder('Mage_Core_Model_Event_Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_modelCacheMock = $this->getMockBuilder('Saas_Saas_Model_Cache')
            ->setConstructorArgs(array(
                $this->getMock('Magento_ObjectManager', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Cache_Frontend_Pool', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Cache_Types', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_ConfigInterface', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false),
                $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false),
                $this->_eventManagerMock
            ))
            ->setMethods(array('_callOriginInvalidateType'))
            ->getMock();
    }

    public function testInvalidateType()
    {
        $this->_eventManagerMock->expects($this->once())->method('dispatch')->with($this->equalTo('refresh_cache'));
        $this->_modelCacheMock->expects($this->once())->method('_callOriginInvalidateType');

        $this->_modelCacheMock->invalidateType('test');
    }
}