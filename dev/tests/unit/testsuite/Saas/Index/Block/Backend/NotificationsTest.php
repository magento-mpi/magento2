<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Block_Backend_NotificationsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Index_Block_Backend_Notifications
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockMock;

    public function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject('Saas_Index_Block_Backend_Notifications', array());
        $this->_blockMock = $this->getMock('Saas_Index_Block_Backend_Notifications',
            array('getUrl'), array(), '', false);
    }

    /**
     * Get url for put index into queue
     *
     * @return string
     */
    public function testGetManageUrl()
    {
        $this->_blockMock->expects($this->once())->method('getUrl')
            ->with('adminhtml/process/list')
            ->will($this->returnValue('some-url'));
        $this->assertEquals('some-url', $this->_blockMock->getManageUrl());
    }
}
