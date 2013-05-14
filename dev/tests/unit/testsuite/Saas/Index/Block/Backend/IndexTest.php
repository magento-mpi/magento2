<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Block_Backend_IndexTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Index_Block_Backend_Index
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_flagMock;

    public function setUp()
    {
        $this->_flagMock = $this->getMock('Saas_Index_Model_Flag',
            array('getState', 'loadSelf', 'isTaskProcessing', 'isTaskAdded'), array(), '', false);
        $this->_flagMock->expects($this->once())->method('loadSelf')->will($this->returnSelf());
        $factoryMock = $this->getMock('Saas_Index_Model_FlagFactory', array('create'), array(), '', false);
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_flagMock));

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManager->getObject('Saas_Index_Block_Backend_Index', array(
            'flagFactory' => $factoryMock,
        ));
    }

    /**
     * @param string $route
     * @param string $method
     * @dataProvider dataProviderUrlMethods
     */
    public function testGetUrlMethods($route, $method)
    {
        $blockMock = $this->getMock('Saas_Index_Block_Backend_Index', array('getUrl'), array(), '', false);
        $blockMock->expects($this->once())->method('getUrl')->with($route)
            ->will($this->returnValue('some-url'));

        $this->assertEquals('some-url', $blockMock->$method());
    }

    /**
     * @return array
     */
    public function dataProviderUrlMethods()
    {
        return array(
            array('adminhtml/saas_index/updateStatus', 'getUpdateStatusUrl'),
            array('adminhtml/saas_index/refresh', 'getRefreshIndexUrl'),
        );
    }

    public function testGetTaskCheckTime()
    {
        $this->assertEquals(Saas_Index_Block_Backend_Index::TASK_TIME_CHECK, $this->_block->getTaskCheckTime());
    }

    /**
     * @param string $method
     * @dataProvider dataProviderDecorationMethods
     */
    public function testDecorationMethods($method)
    {
        $this->_flagMock->expects($this->once())->method($method)->will($this->returnValue('some result'));

        $this->assertEquals('some result', $this->_block->$method());
    }

    /**
     * @return array
     */
    public function dataProviderDecorationMethods()
    {
        return array(
            array('isTaskProcessing'),
            array('isTaskAdded'),
        );
    }
}
