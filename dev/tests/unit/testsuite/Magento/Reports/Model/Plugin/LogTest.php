<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Reports_Model_Plugin_LogTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Reports_Model_Plugin_Log
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_reportEventMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cmpProductIdxMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewProductIdxMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logResourceMock;

    protected function setUp()
    {
        $this->_reportEventMock = $this->getMock(
            'Magento_Reports_Model_Event', array(), array(), '', false
        );
        $this->_cmpProductIdxMock = $this->getMock(
            'Magento_Reports_Model_Product_Index_Compared', array(), array(), '', false
        );
        $this->_viewProductIdxMock = $this->getMock(
            'Magento_Reports_Model_Product_Index_Viewed', array(), array(), '', false
        );

        $this->_logResourceMock = $this->getMock('Magento_Log_Model_Resource_Log', array(), array(), '', false);

        $this->_model = new Magento_Reports_Model_Plugin_Log(
            $this->_reportEventMock,
            $this->_cmpProductIdxMock,
            $this->_viewProductIdxMock
        );
    }

    /**
     * @covers Magento_Reports_Model_Plugin_Log::afterClean
     */
    public function testAfterClean()
    {
        $this->_reportEventMock->expects($this->once())
            ->method('clean');

        $this->_cmpProductIdxMock->expects($this->once())
            ->method('clean');

        $this->_viewProductIdxMock->expects($this->once())
            ->method('clean');

        $this->assertEquals($this->_logResourceMock, $this->_model->afterClean($this->_logResourceMock));
    }
}
