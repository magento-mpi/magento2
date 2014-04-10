<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Error;

/**
 * Class HandlerTest
 * @package Magento\Error
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Error\Handler
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Handler();
    }

    public function testProcessException()
    {
        $expectedMessage = 'test message';

        ob_start();
        $this->object->processException(new \Exception($expectedMessage), []);
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertContains($expectedMessage, $result);
        // assert trace string pieces
        $this->assertContains('internal function', $result);
        $this->assertContains('{main}', $result);
    }

    /**
     * Test handler() method with 'false' result
     *
     * @param int $errorNo
     * @param string $errorStr
     * @param string $errorFile
     * @param int $errorLine
     * @dataProvider handlerProviderNegative
     */
    public function testHandlerNegative($errorNo, $errorStr, $errorFile, $errorLine)
    {
        $result = $this->object->handler($errorNo, $errorStr, $errorFile, $errorLine);
        $this->assertFalse($result);
    }

    public function handlerProviderNegative()
    {
        return [
            [0, 'DateTimeZone::__construct', 0, 0],
            [0, 0, 0, 0]
        ];
    }

    /**
     * @param int $errorNo
     * @param string $errorStr
     * @param string $errorFile
     * @dataProvider handlerProviderPositive
     */
    public function testHandlerPositive($errorNo, $errorStr, $errorFile)
    {
        $result = $this->object->handler($errorNo, $errorStr, $errorFile, 0);
        $this->assertTrue($result);
    }

    public function handlerProviderPositive()
    {
        return [
            [E_STRICT, 'error_string', 'pear'],
            [E_DEPRECATED, 'error_string', 'pear'],
            [E_STRICT, 'pear', 0],
            [E_DEPRECATED, 'pear', 0],
            [E_STRICT, 'pear', 'pear'],
            [E_DEPRECATED, 'pear', 'pear'],
            [E_WARNING, 'open_basedir', 'pear'],
        ];
    }

    /**
     * Test handler() method with 'false' result
     *
     * @param int $errorNo
     * @param string $errorPhrase
     * @dataProvider handlerProviderException
     */
    public function testHandlerException($errorNo, $errorPhrase)
    {
        $errorStr = 'test_string';
        $errorFile = 'test_file';
        $errorLine = 'test_error_line';

        $exceptedExceptionMessage = sprintf('%s: %s in %s on line %s', $errorPhrase, $errorStr, $errorFile, $errorLine);
        $this->setExpectedException('Exception', $exceptedExceptionMessage);

        $this->object->handler($errorNo, $errorStr, $errorFile, $errorLine);
    }

    public function handlerProviderException()
    {
        return [
            [E_ERROR, 'Error'],
            [E_WARNING, 'Warning'],
            [E_PARSE, 'Parse Error'],
            [E_NOTICE, 'Notice'],
            [E_CORE_ERROR, 'Core Error'],
            [E_CORE_WARNING, 'Core Warning'],
            [E_COMPILE_ERROR, 'Compile Error'],
            [E_COMPILE_WARNING, 'Compile Warning'],
            [E_USER_ERROR, 'User Error'],
            [E_USER_WARNING, 'User Warning'],
            [E_USER_NOTICE, 'User Notice'],
            [E_STRICT, 'Strict Notice'],
            [E_RECOVERABLE_ERROR, 'Recoverable Error'],
            [E_DEPRECATED, 'Deprecated Functionality'],
            [E_USER_DEPRECATED, 'User Deprecated Functionality']
        ];
    }

    /**
     * Test handler() method handles unknown error
     */
    public function testHandlerExceptionUnknownError()
    {
        $errorNo = -1;
        $errorStr = 'test_string';
        $errorFile = 'test_file';
        $errorLine = 'test_error_line';
        $errorPhrase = 'Unknown error';

        try {
            $this->object->handler($errorNo, $errorStr, $errorFile, $errorLine);
        } catch (\Exception $e) {
            $this->assertRegExp("/(" . $errorPhrase . ")/i", $e->getMessage());
            $this->assertRegExp("/(" . $errorStr . ")/i", $e->getMessage());
            $this->assertRegExp("/(" . $errorFile . ")/i", $e->getMessage());
            $this->assertRegExp("/(" . $errorLine . ")/i", $e->getMessage());
        }
    }
}
