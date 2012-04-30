<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Set of tests for static code analysis, e.g. code style, code complexity, copy paste detecting, etc.
 */
class Php_LiveCodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_reportDir = '';

    /**
     * @var array
     */
    protected static $_whiteList = array();

    /**
     * @var array
     */
    protected static $_blackList = array();

    public static function setUpBeforeClass()
    {
        self::$_reportDir = Utility_Files::init()->getPathToSource() . '/dev/tests/static/report';
        if (!is_dir(self::$_reportDir)) {
            mkdir(self::$_reportDir, 0777);
        }
        self::$_whiteList = self::_readLists(__DIR__ .'/_files/whitelist/*.txt');
        self::$_blackList = self::_readLists(__DIR__ .'/_files/blacklist/*.txt');
    }

    public function testCodeStyle()
    {
        $reportFile = self::$_reportDir . '/phpcs_report.xml';
        $cmd = new Inspection_CodeSniffer_Command(realpath(__DIR__ . '/_files/phpcs'), $reportFile);
        if (!$cmd->canRun()) {
            $this->markTestSkipped('PHP Code Sniffer command is not available.');
        }
        $cmd->setExtensions(array('php', 'phtml'));
        $this->assertTrue($cmd->run(self::$_whiteList, self::$_blackList, $resInfo), $resInfo['message']);
    }

    public function testCodeMess()
    {
        $reportFile = self::$_reportDir . '/phpmd_report.xml';
        $cmd = new Inspection_MessDetector_Command(realpath(__DIR__ . '/_files/phpmd/ruleset.xml'), $reportFile);
        if (!$cmd->canRun()) {
            $this->markTestSkipped('PHP Mess Detector command line is not available.');
        }
        $this->assertTrue($cmd->run(self::$_whiteList, self::$_blackList, $resInfo), $resInfo['message']);
    }

    public function testCopyPaste()
    {
        $reportFile = self::$_reportDir . '/phpcpd_report.xml';
        $cmd = new Inspection_CopyPasteDetector_Command($reportFile);
        if (!$cmd->canRun()) {
            $this->markTestSkipped('PHP Copy/Paste Detector command line is not available.');
        }
        $this->assertTrue($cmd->run(self::$_whiteList, self::$_blackList, $resInfo), $resInfo['message']);
    }

    /**
     * Read all text files by specified glob pattern and combine them into an array of valid files/directories
     *
     * The Magento root path is prepended to all (non-empty) entries
     *
     * @param string $globPattern
     * @return array
     */
    protected static function _readLists($globPattern)
    {
        $result = array();
        foreach (glob($globPattern) as $list) {
            $result = array_merge($result, file($list));
        }
        $map = function($value) {
            return trim($value) ? Utility_Files::init()->getPathToSource() . '/' . trim($value) : '';
        };
        return array_filter(array_map($map, $result), 'file_exists');
    }
}
