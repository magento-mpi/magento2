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

    public function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject('Saas_Index_Block_Backend_Index', array());
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
}
