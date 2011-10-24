<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Inspection_CopyPasteDetector_CommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Inspection_CopyPasteDetector_Command|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cmd;

    protected function setUp()
    {
        $this->_cmd = $this->getMock(
            'Inspection_CopyPasteDetector_Command',
            array('_execShellCmd'),
            array(
                'some/report/file.xml',
                array('some/test/dir with space', 'some/test/file with space.php'),
                array(),
                5,
                50
            )
        );
    }

    public function canTestDataProvider()
    {
        return array(
            'success' => array(true),
            'failure' => array(false),
        );
    }

    /**
     * @dataProvider canTestDataProvider
     */
    public function testCanRun($expectedResult)
    {
        $this->_cmd
            ->expects($this->once())
            ->method('_execShellCmd')
            ->with($this->stringContains('phpcpd'))
            ->will($this->returnValue($expectedResult))
        ;
        $this->assertEquals($expectedResult, $this->_cmd->canRun());
    }

    public function getVersionDataProvider()
    {
        return array(
            array('phpcpd 1.3.2 by Sebastian Bergmann.', '1.3.2'),
        );
    }

    /**
     * @dataProvider getVersionDataProvider
     */
    public function testGetVersion($versionCmdOutput, $expectedVersion)
    {
        $cmdCallback = function ($shellCmd, array &$output = null) use ($versionCmdOutput)
        {
            $output = array($versionCmdOutput);
            return !empty($shellCmd);
        };
        $this->_cmd
            ->expects($this->once())
            ->method('_execShellCmd')
            ->with($this->stringContains('phpcpd'))
            ->will($this->returnCallback($cmdCallback))
        ;
        $this->assertEquals($expectedVersion, $this->_cmd->getVersion());
    }

    public function testRun()
    {
        $expectedQuoteChar = substr(escapeshellarg(' '), 0, 1);
        $expectedCmd = 'phpcpd'
            . ' --log-pmd "some/report/file.xml"'
            . ' --min-lines 5'
            . ' --min-tokens 50'
            . ' "some/test/dir with space" "some/test/file with space.php"'
        ;
        $expectedCmd = str_replace('"', $expectedQuoteChar, $expectedCmd);
        $this->_cmd
            ->expects($this->once())
            ->method('_execShellCmd')
            ->with($expectedCmd)
        ;
        $this->_cmd->run();
    }
}
