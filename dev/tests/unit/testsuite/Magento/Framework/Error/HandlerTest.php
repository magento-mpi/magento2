<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Error;

/**
 * Class HandlerTest
 * @package Magento\Framework\Error
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Error\Handler
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Handler();
    }

    public function testProcessException()
    {
        $expectedMessage = 'test message';

        $this->expectOutputRegex('/(' . $expectedMessage . ')\s*?.*(internal function)((.*\s.*)*)({main})/');
        $this->object->processException(new \Exception($expectedMessage), []);
    }

    /**
     * @param int $errorNo
     * @param string $errorStr
     * @param string $errorFile
     * @param bool $expectedResult
     * @dataProvider handlerProvider
     */
    public function testHandler($errorNo, $errorStr, $errorFile, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->object->handler($errorNo, $errorStr, $errorFile, 11));
    }

    public function handlerProvider()
    {
        return [
            [E_STRICT, 'error_string', 'pear', true],
            [E_DEPRECATED, 'error_string', 'pear', true],
            [E_STRICT, 'pear', 0, true],
            [E_DEPRECATED, 'pear', 0, true],
            [E_STRICT, 'pear', 'pear', true],
            [E_DEPRECATED, 'pear', 'pear', true],
            [E_WARNING, 'open_basedir', 'pear', true],
            [0, 'DateTimeZone::__construct', 0, false],
            [0, 0, 0, false]
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
            [E_USER_DEPRECATED, 'User Deprecated Functionality'],
            ['42', 'Unknown error (42)']
        ];
    }
}
