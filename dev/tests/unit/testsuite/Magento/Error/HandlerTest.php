<?php
/**
 * Unit Test for \Magento\Filesystem\Stream\Mode
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Error;

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

    protected function setUp()
    {
        $this->_loggerMock = $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false);
        $this->_dirMock = $this->getMock('Magento\Core\Model\Dir', array(), array(BP), '', true);
    }

    /**
     * @covers \Magento\Error\Handler::__construct
     * @covers \Magento\Error\Handler::processException
     */
    public function testProcessExceptionPrint()
    {
        $handler = new \Magento\Error\Handler($this->_loggerMock, $this->_dirMock, true);
        $exception = new \Exception('TestMessage');

        ob_start();
        $handler->processException($exception);
        $actualResult = ob_get_contents();
        ob_end_clean();
        $this->assertRegExp('/TestMessage/', $actualResult);
    }

    /**
     * @covers \Magento\Error\Handler::__construct
     * @covers \Magento\Error\Handler::processException
     */
    public function testProcessExceptionReport()
    {
        $handler = new \Magento\Error\Handler($this->_loggerMock, $this->_dirMock, false);
        $this->_dirMock->expects($this->atLeastOnce())
            ->method('getDir')
            ->with(\Magento\Core\Model\Dir::PUB)
            ->will($this->returnValue(dirname(__DIR__) . DS . '_files'));

        $exception = new \Exception('TestMessage');
        $handler->processException($exception);
    }

    /**
     * @covers \Magento\Error\Handler::__construct
     * @covers \Magento\Error\Handler::handler
     * @throws \Exception
     */
    public function testErrorHandlerLogging()
    {
        $handler = new \Magento\Error\Handler($this->_loggerMock, $this->_dirMock, false);
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
     * @covers \Magento\Error\Handler::__construct
     * @covers \Magento\Error\Handler::handler
     * @expectedException \Exception
     * @throws \Exception
     */
    public function testErrorHandlerPrint()
    {
        $handler = new \Magento\Error\Handler($this->_loggerMock, $this->_dirMock, true);
        set_error_handler(array($handler, 'handler'));
        try {
            trigger_error('testErrorHandlerPrint', E_USER_NOTICE);
        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }
    }
}
