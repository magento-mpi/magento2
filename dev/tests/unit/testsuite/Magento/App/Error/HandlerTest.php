<?php
/**
 * Unit Test for \Magento\App\Error\Handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Error;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loggerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    protected function setUp()
    {
        $this->_loggerMock = $this->getMock('Magento\Logger', array(), array(), '', false);
        $this->_dirMock = $this->getMock('Magento\App\Dir', array(), array(BP), '', true);
        $this->_appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
    }

    /**
     * @covers \Magento\Error\Handler::processException
     */
    public function testProcessExceptionPrint()
    {
        $handler = new \Magento\App\Error\Handler($this->_loggerMock, $this->_dirMock, $this->_appStateMock);
        $this->_appStateMock->expects($this->any())->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_DEVELOPER));
        $exception = new \Exception('TestMessage');

        ob_start();
        $handler->processException($exception);
        $actualResult = ob_get_contents();
        ob_end_clean();
        $this->assertRegExp('/TestMessage/', $actualResult);
    }

    /**
     * @covers \Magento\Error\Handler::processException
     */
    public function testProcessExceptionReport()
    {
        $handler = new \Magento\App\Error\Handler($this->_loggerMock, $this->_dirMock, $this->_appStateMock);
        $this->_appStateMock->expects($this->any())->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_DEFAULT));
        $this->_dirMock->expects($this->atLeastOnce())
            ->method('getDir')
            ->with(\Magento\App\Dir::PUB)
            ->will($this->returnValue(dirname(__DIR__) . '/../_files'));

        $exception = new \Exception('TestMessage');
        $handler->processException($exception);
    }

    /**
     * @covers \Magento\Error\Handler::handler
     * @throws \Exception
     */
    public function testErrorHandlerLogging()
    {
        $handler = new \Magento\App\Error\Handler($this->_loggerMock, $this->_dirMock, $this->_appStateMock);
        $this->_appStateMock->expects($this->any())->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_DEFAULT));
        $this->_loggerMock->expects($this->once())
            ->method('log')
            ->with($this->stringContains('testErrorHandlerLogging'), \Zend_Log::ERR);
        set_error_handler(array($handler, 'handler'));
        try {
            trigger_error('testErrorHandlerLogging', E_USER_NOTICE);
            restore_error_handler();
        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }
    }

    /**
     * @covers \Magento\Error\Handler::handler
     * @expectedException \Exception
     * @throws \Exception
     */
    public function testErrorHandlerPrint()
    {
        $handler = new \Magento\App\Error\Handler($this->_loggerMock, $this->_dirMock, $this->_appStateMock);
        $this->_appStateMock->expects($this->any())->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_DEVELOPER));
        set_error_handler(array($handler, 'handler'));
        try {
            trigger_error('testErrorHandlerPrint', E_USER_NOTICE);
        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }
    }
}
