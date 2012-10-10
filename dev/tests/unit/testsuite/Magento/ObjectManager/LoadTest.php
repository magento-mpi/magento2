<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ObjectManager_LoadTest extends PHPUnit_Framework_TestCase
{
    /**
     * ObjectManager instance for tests
     *
     * @var Magento_ObjectManager_Zend
     */
    protected $_objectManager;

    /**
     * Area code
     */
    const AREA_CODE = 'global';

    protected function setUp()
    {
        /** @var $modelConfigMock Mage_Core_Model_Config */
        $modelConfigMock = $this->getMock('Mage_Core_Model_Config', array('getNode', 'loadBase'), array(), '', false);
        $modelConfigMock->expects($this->exactly(2))
            ->method('getNode')
            ->will($this->returnCallback(
                array($this, 'getNodeCallback')
            ));

        /** @var $instanceManagerMock Zend\Di\InstanceManager */
        $instanceManagerMock = $this->getMock('Zend\Di\InstanceManager', array('addSharedInstance', 'addAlias'),
            array(), '', false
        );
        $instanceManagerMock->expects($this->exactly(2))
            ->method('addAlias');

        /** @var $diMock Zend\Di\Di */
        $diMock = $this->getMock('Zend\Di\Di', array('instanceManager', 'get'), array(), '', false);
        $diMock->expects($this->exactly(3))
            ->method('instanceManager')
            ->will($this->returnValue($instanceManagerMock));
        $diMock->expects($this->exactly(3))
            ->method('get')
            ->will($this->returnValue($modelConfigMock));

        $this->_objectManager = new Magento_ObjectManager_Zend(null, $diMock);
    }

    protected function tearDown()
    {
        unset($this->_objectManager);
    }

    /**
     * @covers Magento_ObjectManager_Zend::loadAreaConfiguration
     */
    public function testLoadAreaConfiguration()
    {
        $this->_objectManager->loadAreaConfiguration(self::AREA_CODE);
    }

    /**
     * Check passed param and retrieve mock of node object
     *
     * @param string $path
     * @return Varien_Object|PHPUnit_Framework_MockObject_MockObject
     */
    public function getNodeCallback($path)
    {
        $this->assertEquals(self::AREA_CODE . '/' . Magento_ObjectManager_Zend::CONFIGURATION_DI_NODE, $path);

        $nodeMock = $this->getMock('Varien_Object', array('asArray'), array(), '', false);
        $nodeMock->expects($this->once())
            ->method('asArray')
            ->will($this->returnValue(
                array(
                    'alias' => array(1)
                )
            ));

        return $nodeMock;
    }
}
