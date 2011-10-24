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

class Inspection_CodeSniffer_CommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Inspection_CodeSniffer_Command|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cmd;

    protected function setUp()
    {
        $this->_cmd = $this->getMock(
            'Inspection_CodeSniffer_Command',
            array('_execShellCmd'),
            array(
                'some/ruleset/dir',
                'some/report/file.xml',
                array('some/test/dir with space', 'some/test/file with space.php')
            )
        );
    }

    /**
     * @dataProvider canRunDataProvider
     */
    public function testCanRun($expectedResult)
    {
        $this->_cmd
            ->expects($this->once())
            ->method('_execShellCmd')
            ->with($this->stringContains('phpcs'))
            ->will($this->returnValue($expectedResult))
        ;
        $this->assertEquals($expectedResult, $this->_cmd->canRun());
    }

    public function canRunDataProvider()
    {
        return array(
            'success' => array(true),
            'failure' => array(false),
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
            ->with($this->stringContains('phpcs'))
            ->will($this->returnCallback($cmdCallback))
        ;
        $this->assertEquals($expectedVersion, $this->_cmd->getVersion());
    }

    public function getVersionDataProvider()
    {
        return array(
            array('PHP_CodeSniffer version 1.3.0RC2 (beta) by Squiz Pty Ltd. (http://www.squiz.net)', '1.3.0RC2'),
            array('PHP_CodeSniffer version 1.3.0 (stable) by Squiz Pty Ltd. (http://www.squiz.net)',  '1.3.0'),
        );
    }

    public function testRun()
    {
        $expectedQuoteChar = substr(escapeshellarg(' '), 0, 1);
        $expectedCmd = 'phpcs'
            . ' --standard="some/ruleset/dir"'
            . ' --report=checkstyle'
            . ' --report-file="some/report/file.xml"'
            . ' -n'
            . ' "some/test/dir with space"'
            . ' "some/test/file with space.php"'
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
