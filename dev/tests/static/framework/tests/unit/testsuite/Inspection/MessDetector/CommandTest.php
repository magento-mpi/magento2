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

class Inspection_MessDetector_CommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Inspection_MessDetector_Command|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cmd;

    protected function setUp()
    {
        $this->_cmd = $this->getMock(
            'Inspection_MessDetector_Command',
            array('_execShellCmd'),
            array(
                'some/ruleset/file.xml',
                'some/report/file.xml',
                array('some/test/dir with space', 'some/test/file with space.php')
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
            ->with($this->stringContains('phpmd'))
            ->will($this->returnValue($expectedResult))
        ;
        $this->assertEquals($expectedResult, $this->_cmd->canRun());
    }

    public function getVersionDataProvider()
    {
        return array(
            array('PHPMD 0.2.8RC1 by Manuel Pichler', '0.2.8RC1'),
            array('PHPMD 1.1.1 by Manuel Pichler',    '1.1.1'),
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
            ->with($this->stringContains('phpmd'))
            ->will($this->returnCallback($cmdCallback))
        ;
        $this->assertEquals($expectedVersion, $this->_cmd->getVersion());
    }

    public function testRun()
    {
        $expectedQuoteChar = substr(escapeshellarg(' '), 0, 1);
        $expectedCmd = 'phpmd'
            . ' "some/test/dir with space,some/test/file with space.php"'
            . ' xml'
            . ' "some/ruleset/file.xml"'
            . ' --reportfile "some/report/file.xml"'
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
