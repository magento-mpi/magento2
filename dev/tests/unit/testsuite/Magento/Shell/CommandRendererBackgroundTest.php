<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shell;

class CommandRendererBackgroundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test data for command
     *
     * @var string
     */
    protected $testCommand = 'php -r test.php';

    /**
     * @var \Magento\OsInfo|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $osInfo;

    public function setUp()
    {
        $this->osInfo = $this->getMockBuilder('Magento\OsInfo')->getMock();
    }

    /**
     * @dataProvider commandPerOsTypeDataProvider
     * @param bool $isWindows
     * @param string $expectedResults
     */
    public function testRender($isWindows, $expectedResults)
    {
        $this->osInfo->expects($this->once())
            ->method('isWindows')
            ->will($this->returnValue($isWindows));

        $commandRenderer = new CommandRendererBackground($this->osInfo);
        $this->assertEquals(
            $expectedResults,
            $commandRenderer->render($this->testCommand)
        );
    }

    /**
     * Data provider for each os type
     *
     * @return array
     */
    public function commandPerOsTypeDataProvider()
    {
        return array(
            'windows' => array(true, 'start /B "magento background task" ' . $this->testCommand . ' 2>&1'),
            'unix'    => array(false, $this->testCommand . ' 2>&1 > /dev/null &'),
        );
    }
}
