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

class Php_LiveCodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_reportDir;

    /**
     * @var array
     */
    protected $_whiteList;

    /**
     * @var array
     */
    protected $_blackList;

    protected function setUp()
    {
        if (!$this->_reportDir) {
            $config = require(__DIR__ . '/config.php');
            $this->_reportDir = $config['report_dir'];
            if (!is_dir($this->_reportDir)) {
                mkdir($this->_reportDir, 0777, true);
            }
            $this->_whiteList = $config['white_list'];
            $this->_blackList = $config['black_list'];
        }
    }

    public function testCodeStyle()
    {
        $reportFile = $this->_reportDir . '/phpcs_report.xml';
        $cmd = new Inspection_CodeSniffer_Command(
            realpath(__DIR__ . '/_files/phpcs'),
            $reportFile,
            $this->_whiteList,
            $this->_blackList
        );
        if (!$cmd->canRun()) {
            $this->markTestSkipped('PHP Code Sniffer command line is not available.');
        }
        $this->assertTrue($cmd->run(), "See detailed report in '{$reportFile}'.");
    }

    public function testCodeMess()
    {
        $reportFile = $this->_reportDir . '/phpmd_report.xml';
        $cmd = new Inspection_MessDetector_Command(
            realpath(__DIR__ . '/_files/phpmd/ruleset.xml'),
            $reportFile,
            $this->_whiteList,
            $this->_blackList
        );
        if (!$cmd->canRun()) {
            $this->markTestSkipped('PHP Mess Detector command line is not available.');
        }
        $this->assertTrue($cmd->run(), "See detailed report in '{$reportFile}'.");
    }

    public function testCopyPaste()
    {
        $reportFile = $this->_reportDir . '/phpcpd_report.xml';
        $cmd = new Inspection_CopyPasteDetector_Command(
            $reportFile,
            $this->_whiteList,
            $this->_blackList
        );
        if (!$cmd->canRun()) {
            $this->markTestSkipped('PHP Copy/Paste Detector command line is not available.');
        }
        $this->assertTrue($cmd->run(), "See detailed report in '{$reportFile}'.");
    }
}
