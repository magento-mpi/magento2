<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Event\Observer;

/**
 * Class RegexTest
 * @package Magento\Event\Observer
 */
class RegexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Regex
     */
    protected $regex;

    protected function setUp()
    {
        $this->regex = new Regex();
    }

    protected function tearDown()
    {
        $this->regex = null;
    }

    /**
     * @dataProvider isValidForProvider
     * @param string $pattern
     * @param string $name
     * @param bool $expectedResult
     */
    public function testIsValidFor($pattern, $name, $expectedResult)
    {
        $this->regex->setEventRegex($pattern);
        $eventMock = $this->getMock('Magento\Event', [], [], '', false);
        $eventMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        $this->assertEquals($expectedResult, $this->regex->isValidFor($eventMock));
    }

    public function isValidForProvider()
    {
        return [
            ['~_name$~', 'event_name', true],
            ['~_names$~', 'event_name', false]
        ];
    }
}
