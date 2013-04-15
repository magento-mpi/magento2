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
    protected $_blockMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_flagMock;

    public function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $factoryMock = $this->getMock('Saas_Index_Model_FlagFactory', array('create'), array(), '', false);
        $this->_flagMock = $this->getMock('Saas_Index_Model_Flag', array('getState', 'loadSelf'), array(), '', false);
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_flagMock));

        $arguments = array(
            'flagFactory' => $factoryMock,
        );
        $this->_block = $objectManagerHelper->getObject('Saas_Index_Block_Backend_Index', $arguments);
        $this->_blockMock = $this->getMock('Saas_Index_Block_Backend_Index', array('getUrl'), array(), '', false);
    }

    public function testGetUpdateStatusUrl()
    {
        $this->_blockMock->expects($this->once())->method('getUrl')
            ->with('adminhtml/saas_index/updateStatus')
            ->will($this->returnValue('some-url'));
        $this->assertEquals('some-url', $this->_blockMock->getUpdateStatusUrl());
    }

    public function testGetRefreshIndexUrl()
    {
        $this->_blockMock->expects($this->once())->method('getUrl')
            ->with('adminhtml/saas_index/refresh')
            ->will($this->returnValue('some-url'));
        $this->assertEquals('some-url', $this->_blockMock->getRefreshIndexUrl());
    }

    public function testGetCancelIndexUrl()
    {
        $this->_blockMock->expects($this->once())->method('getUrl')
            ->with('adminhtml/saas_index/cancel')
            ->will($this->returnValue('some-url'));
        $this->assertEquals('some-url', $this->_blockMock->getCancelIndexUrl());
    }

    public function testGetTaskCheckTime()
    {
        $this->assertEquals(Saas_Index_Block_Backend_Index::TASK_TIME_CHECK, $this->_block->getTaskCheckTime());
    }

    public function testIsTaskProcessingWithNotProcessingState()
    {
        $this->_flagMock->expects($this->once())
            ->method('getState')->will($this->returnValue(Saas_Index_Model_Flag::STATE_QUEUED));
        $this->assertFalse($this->_block->isTaskProcessing());
    }

    public function testIsTaskProcessingWithProcessingState()
    {
        $this->_flagMock->expects($this->once())
            ->method('getState')->will($this->returnValue(Saas_Index_Model_Flag::STATE_PROCESSING));
        $this->assertTrue($this->_block->isTaskProcessing());
    }

    public function testIsTaskAddedWithFinishedState()
    {
        $this->_flagMock->expects($this->any())
            ->method('getState')->will($this->returnValue(Saas_Index_Model_Flag::STATE_FINISHED));
        $this->assertFalse($this->_block->isTaskAdded());
    }

    public function testIsTaskAddedWithQueuedState()
    {
        $this->_flagMock->expects($this->any())
            ->method('getState')->will($this->returnValue(Saas_Index_Model_Flag::STATE_QUEUED));
        $this->assertTrue($this->_block->isTaskAdded());
    }
}
