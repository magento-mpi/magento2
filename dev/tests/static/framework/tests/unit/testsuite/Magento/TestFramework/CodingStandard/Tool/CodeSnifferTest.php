<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\CodingStandard\Tool;

class CodeSnifferTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\CodingStandard\Tool\CodeSniffer
     */
    protected $_tool;

    /**
     * @var PHP_CodeSniffer_CLI
     */
    protected $_wrapper;

    /**
     * Rule set directory
     */
    const RULE_SET = 'some/ruleset/directory';

    /**
     * Report file
     */
    const REPORT_FILE = 'some/report/file.xml';

    protected function setUp()
    {
        $this->_wrapper = $this->getMock('Magento\TestFramework\CodingStandard\Tool\CodeSniffer\Wrapper');
        $this->_tool = new \Magento\TestFramework\CodingStandard\Tool\CodeSniffer(
            self::RULE_SET,
            self::REPORT_FILE,
            $this->_wrapper
        );
    }

    public function testRun()
    {
        $whiteList = array('test' . rand(), 'test' . rand());
        $blackList = array('test' . rand(), 'test' . rand());
        $extensions = array('test' . rand(), 'test' . rand());

        $this->_wrapper->expects($this->once())->method('getDefaults')->will($this->returnValue(array()));

        $expectedCliEmulation = array(
            'files' => $whiteList,
            'standard' => [self::RULE_SET],
            'ignored' => $blackList,
            'extensions' => $extensions,
            'reportFile' => self::REPORT_FILE,
            'warningSeverity' => 0,
            'reports' => array('checkstyle' => null)
        );

        $this->_wrapper->expects($this->once())->method('setValues')->with($this->equalTo($expectedCliEmulation));

        $this->_wrapper->expects($this->once())->method('process');

        $this->_tool->run($whiteList, $blackList, $extensions);
    }
}
