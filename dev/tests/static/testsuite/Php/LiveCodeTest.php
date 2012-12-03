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
        self::$_whiteList = self::_readLists(__DIR__ . '/_files/whitelist/*.txt');
        self::$_blackList = self::_readLists(__DIR__ . '/_files/blacklist/*.txt');
    }

    public function testCodeStyle()
    {
        $reportFile = self::$_reportDir . '/phpcs_report.xml';
        $tool = new CodingStandard_Tool_CodeSniffer_Wrapper();
        $cs = new CodingStandard_Tool_CodeSniffer(realpath(__DIR__ . '/_files/phpcs'), $reportFile, $tool);
        if (!$cs->canRun()) {
            $this->markTestSkipped('PHP Code Sniffer is not installed.');
        }
        $result = $cs->run(self::$_whiteList, self::$_blackList, array('php', 'phtml'));
        $this->assertEquals(0, $result,
            "PHP Code Sniffer has found $result error(s): See detailed report in $reportFile"
        );
    }

    public function testCodeMess()
    {
        $reportFile = self::$_reportDir . '/phpmd_report.xml';
        $cmd = new Inspection_MessDetector_Command(realpath(__DIR__ . '/_files/phpmd/ruleset.xml'), $reportFile);
        if (!$cmd->canRun()) {
            $this->markTestSkipped('PHP Mess Detector command line is not available.');
        }
        $this->assertTrue($cmd->run(self::$_whiteList, self::$_blackList), $cmd->getLastRunMessage());
    }

    public function testCopyPaste()
    {
        $reportFile = self::$_reportDir . '/phpcpd_report.xml';
        $cmd = new Inspection_CopyPasteDetector_Command($reportFile);
        if (!$cmd->canRun()) {
            $this->markTestSkipped('PHP Copy/Paste Detector command line is not available.');
        }
        $this->assertTrue($cmd->run(self::$_whiteList, self::$_blackList), $cmd->getLastRunMessage());
    }

    /**
     * Read all text files by specified glob pattern and combine them into an array of valid files/directories
     *
     * The Magento root path is prepended to all (non-empty) entries
     *
     * @param string $globPattern
     * @return array
     * @throws Exception if any of the patterns don't return any result
     */
    protected static function _readLists($globPattern)
    {
        $patterns = array();
        foreach (glob($globPattern) as $list) {
            $patterns = array_merge($patterns, file($list, FILE_IGNORE_NEW_LINES));
        }
        $result = array();
        foreach ($patterns as $pattern) {
            if (0 === strpos($pattern, '#')) {
                continue;
            }
            /**
             * Note that glob() for directories will be returned as is,
             * but passing directory is supported by the tools (phpcpd, phpmd, phpcs)
             */
            $files = glob(Utility_Files::init()->getPathToSource() . '/' . $pattern, GLOB_BRACE);
            if (empty($files)) {
                throw new Exception("The glob() pattern '{$pattern}' didn't return any result.");
            }
            $result = array_merge($result, $files);
        }
        return $result;
    }
}
