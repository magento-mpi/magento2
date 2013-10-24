<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Logger|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model = null;

    /**
     * @var \ReflectionProperty
     */
    protected $_loggersProperty = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    protected function setUp()
    {
        $this->_filesystemMock = $this->getMock('Magento\Io\File', array(), array(), '', false, false);
        $dirs = new \Magento\App\Dir(TESTS_TEMP_DIR);
        $logDir = $dirs->getDir(\Magento\App\Dir::LOG);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $this->_model = new \Magento\Logger($dirs, $this->_filesystemMock);
        $this->_loggersProperty = new \ReflectionProperty($this->_model, '_loggers');
        $this->_loggersProperty->setAccessible(true);
    }

    /**
     * @param string $key
     * @param string $fileOrWrapper
     * @dataProvider addStreamLogDataProvider
     */
    public function testAddStreamLog($key, $fileOrWrapper)
    {
        $this->assertFalse($this->_model->hasLog($key));
        $this->_model->addStreamLog($key, $fileOrWrapper);
        $this->assertTrue($this->_model->hasLog($key));

        $loggers = $this->_loggersProperty->getValue($this->_model);
        $this->assertArrayHasKey($key, $loggers);
        $zendLog = $loggers[$key];
        $this->assertInstanceOf('Zend_Log', $zendLog);

        $writersProperty = new \ReflectionProperty($zendLog, '_writers');
        $writersProperty->setAccessible(true);
        $writers = $writersProperty->getValue($zendLog);
        $this->assertArrayHasKey(0, $writers);
        $stream = $writers[0];
        $this->assertInstanceOf('Zend_Log_Writer_Stream', $writers[0]);

        $streamProperty = new \ReflectionProperty($stream, '_stream');
        $streamProperty->setAccessible(true);
        $fileOrWrapper = $streamProperty->getValue($stream);
        $this->assertInternalType('resource', $fileOrWrapper);
        $this->assertEquals('stream', get_resource_type($fileOrWrapper));
    }

    /**
     * @return array
     */
    public function addStreamLogDataProvider()
    {
        return array(
            array('test', 'php://output'),
            array('test', 'custom_file.log'),
            array('test', ''),
        );
    }

    public function testLog()
    {
        $messageOne = uniqid();
        $messageTwo = uniqid();
        $messageThree = uniqid();
        $this->expectOutputRegex('/' . 'DEBUG \(7\).+?' . $messageTwo . '.+?' . 'CRIT \(2\).+?' . $messageThree . '/s');
        $this->_model->addStreamLog('test', 'php://output');
        $this->_model->log($messageOne);
        $this->_model->log($messageTwo, \Zend_Log::DEBUG, 'test');
        $this->_model->log($messageThree, \Zend_Log::CRIT, 'test');
    }

    public function testLogComplex()
    {
        $this->expectOutputRegex('/Array\s\(\s+\[0\] => 1\s\).+stdClass Object/s');
        $this->_model->addStreamLog(\Magento\Logger::LOGGER_SYSTEM, 'php://output');
        $this->_model->log(array(1));
        $this->_model->log(new \StdClass);
    }

    public function testLogDebug()
    {
        $message = uniqid();
        /** @var $model \Magento\Logger|PHPUnit_Framework_MockObject_MockObject */
        $model = $this->getMock('Magento\Logger', array('log'), array(), '', false);
        $model->expects($this->at(0))->method('log')
            ->with($message, \Zend_Log::DEBUG, \Magento\Logger::LOGGER_SYSTEM);
        $model->expects($this->at(1))->method('log')
            ->with($message, \Zend_Log::DEBUG, \Magento\Logger::LOGGER_EXCEPTION);
        $model->logDebug($message);
        $model->logDebug($message, \Magento\Logger::LOGGER_EXCEPTION);
    }

    public function testLogException()
    {
        $exception = new \Exception;
        $expected = "\n{$exception}";
        /** @var $model \Magento\Logger|PHPUnit_Framework_MockObject_MockObject */
        $model = $this->getMock('Magento\Logger', array('log'), array(), '', false);
        $model->expects($this->at(0))->method('log')
            ->with($expected, \Zend_Log::ERR, \Magento\Logger::LOGGER_EXCEPTION);
        $model->logException($exception);
    }
}
