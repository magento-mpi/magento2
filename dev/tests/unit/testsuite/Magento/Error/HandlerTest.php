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
        $this->expectOutputRegex("/(" . $expectedMessage . ")/i");
        $this->object->processException(new \Exception($expectedMessage), array());
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
        return array(
            array(0, 'DateTimeZone::__construct', 0, 0),
            array(0, 0, 0, 0)
        );
    }

    /**
     * @param int $errorNo
     * @param string $errorStr
     * @param string $errorFile
     * @param int $errorLine
     * @dataProvider handlerProviderPositive
     */
    public function testHandlerPositive($errorNo, $errorStr, $errorFile, $errorLine)
    {
        $result = $this->object->handler($errorNo, $errorStr, $errorFile, $errorLine);
        $this->assertTrue($result);
    }

    public function handlerProviderPositive()
    {
        return array(
            array(E_STRICT, 0, 'pear', 0),
            array(E_DEPRECATED, 0, 'pear', 0),
            array(E_STRICT, 'pear', 0, 0),
            array(E_DEPRECATED, 'pear', 0, 0),
            array(E_STRICT, 'pear', 'pear', 0),
            array(E_DEPRECATED, 'pear', 'pear', 0),
            array(E_WARNING, 'open_basedir', 'pear', 0),
        );
    }

    /**
     * Test handler() method with 'false' result
     *
     * @param int $errorNo
     * @param string $errorStr
     * @param string $errorFile
     * @param int $errorLine
     * @dataProvider handlerProviderException
     */
    public function testHandlerException($errorNo, $errorStr, $errorFile, $errorLine)
    {
        try {
            $this->object->handler($errorNo, $errorStr, $errorFile, $errorLine);
        } catch (\Exception $e) {
            $this->assertRegExp("/" . $errorStr . "/i", $e->getMessage());
        }
    }

    public function handlerProviderException()
    {
        return array(
            array(E_ERROR, '', 0, 0),
            array(-1, '', 0, 0),
            array(E_WARNING, '', 0, 0),
            array(E_PARSE, '', 0, 0),
            array(E_NOTICE, '', 0, 0),
            array(E_CORE_ERROR, '', 0, 0),
            array(E_CORE_WARNING, '', 0, 0),
            array(E_COMPILE_ERROR, '', 0, 0),
            array(E_COMPILE_WARNING, '', 0, 0),
            array(E_USER_ERROR, '', 0, 0),
            array(E_USER_WARNING, '', 0, 0),
            array(E_USER_NOTICE, '', 0, 0),
            array(E_STRICT, '', 0, 0),
            array(E_RECOVERABLE_ERROR, '', 0, 0),
            array(E_DEPRECATED, '', 0, 0),
            array(E_USER_DEPRECATED, '', 0, 0),
        );
    }
}
