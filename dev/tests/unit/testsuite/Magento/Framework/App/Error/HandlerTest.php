<?php
/**
 * Unit Test for \Magento\Framework\App\Error\Handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Error;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Logger mock
     *
     * @var \Magento\Framework\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * Filesystem mock
     *
     * @var \Magento\Framework\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * App state mock
     *
     * @var  \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appState;

    /**
     * Handler instance
     *
     * @var \Magento\Framework\App\Error\Handler
     */
    protected $handler;

    protected function setUp()
    {
        $this->logger = $this->getMock('Magento\Framework\Logger', array(), array(), '', false);
        $this->filesystem = $this->getMock('Magento\Framework\App\Filesystem', array(), array(), '', false);
        $this->appState = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);
        $this->handler = new \Magento\Framework\App\Error\Handler($this->logger, $this->filesystem, $this->appState);
    }

    /**
     * Test for processException method print
     *
     * @covers \Magento\Error\Handler::processException
     */
    public function testProcessExceptionPrint()
    {
        $this->appState->expects(
            $this->any()
        )->method(
            'getMode'
        )->will(
            $this->returnValue(\Magento\Framework\App\State::MODE_DEVELOPER)
        );
        $exception = new \Exception('TestMessage');

        ob_start();
        $this->handler->processException($exception);
        $actualResult = ob_get_contents();
        ob_end_clean();
        $this->assertRegExp('/TestMessage/', $actualResult);
    }

    /**
     * Test for processException method report
     *
     * @covers \Magento\Error\Handler::processException
     * @runInSeparateProcess
     */
    public function testProcessExceptionReport()
    {
        $this->appState->expects(
            $this->any()
        )->method(
            'getMode'
        )->will(
            $this->returnValue(\Magento\Framework\App\State::MODE_DEFAULT)
        );
        $this->filesystem->expects(
            $this->atLeastOnce()
        )->method(
            'getPath'
        )->with(
            \Magento\Framework\App\Filesystem::PUB_DIR
        )->will(
            $this->returnValue(dirname(__DIR__) . '/../../_files')
        );

        $exception = new \Exception('TestMessage');
        $this->handler->processException($exception);
    }

    /**
     * Test for setting error handler and logging
     *
     * @covers \Magento\Error\Handler::handler
     * @throws \Exception
     */
    public function testErrorHandlerLogging()
    {
        $this->appState->expects(
            $this->any()
        )->method(
            'getMode'
        )->will(
            $this->returnValue(\Magento\Framework\App\State::MODE_DEFAULT)
        );
        $this->logger->expects(
            $this->once()
        )->method(
            'log'
        )->with(
            $this->stringContains('testErrorHandlerLogging'),
            \Zend_Log::ERR
        );
        set_error_handler(array($this->handler, 'handler'));
        try {
            trigger_error('testErrorHandlerLogging', E_USER_NOTICE);
            restore_error_handler();
        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }
    }

    /**
     * Test for setting error handler and printing
     *
     * @covers \Magento\Error\Handler::handler
     * @expectedException \Exception
     * @throws \Exception
     */
    public function testErrorHandlerPrint()
    {
        $this->appState->expects(
            $this->any()
        )->method(
            'getMode'
        )->will(
            $this->returnValue(\Magento\Framework\App\State::MODE_DEVELOPER)
        );
        set_error_handler(array($this->handler, 'handler'));
        try {
            trigger_error('testErrorHandlerPrint', E_USER_NOTICE);
        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }
    }
}
