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
 * JSHint static code analysis tests for javascript files
 */
class Js_LiveCodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_reportFile = '';

    /**
     * @var array
     */
    protected static $_whiteListJsFiles = array();

    /**
     * @var array
     */
    protected static $_blackListJsFiles = array();

    /**
     * @static Return all files under a path
     * @param string $path
     * @param array $name
     * @return array
     */
    protected static function _scanFileNameRecursivly($path = '', &$name = array())
    {
        if (is_file($path)) {
            return array($path);
        }
        $path = $path == '' ? dirname(__FILE__) : $path;
        $dirs = glob($path . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT);
        $jsFiles = glob($path . DIRECTORY_SEPARATOR .'*.js', GLOB_NOSORT);
        foreach ($dirs as $dir) {
            self::_scanFileNameRecursivly($dir, &$name);
        }
        foreach ($jsFiles as $jsFile) {
            $name[] = $jsFile;
        }
        return $name;
    }

    /**
     * @static Setup report file, black list and white list
     *
     */
    public static function setUpBeforeClass()
    {
        $reportDir = Utility_Files::init()->getPathToSource() . '/dev/tests/static/report';
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0777);
        }
        self::$_reportFile = $reportDir . '/js_report.txt';
        @unlink(self::$_reportFile);
        $whiteList = self::_readLists(__DIR__ . '/_files/whitelist/*.txt');
        $blackList = self::_readLists(__DIR__ . '/_files/blacklist/*.txt');
        foreach ($blackList as $listFiles) {
            self::$_blackListJsFiles = array_merge(self::$_blackListJsFiles, self::_scanFileNameRecursivly($listFiles));
        }
        foreach ($whiteList as $listFiles) {
            self::$_whiteListJsFiles = array_merge(self::$_whiteListJsFiles, self::_scanFileNameRecursivly($listFiles));
        }
        $blackListJsFiles = self::$_blackListJsFiles;
        $filter = function($value) use($blackListJsFiles)
        {
            return !in_array($value, $blackListJsFiles);
        };
        self::$_whiteListJsFiles = array_filter(self::$_whiteListJsFiles, $filter);
    }

    protected function _isTestRunnable($filename){
        $command = 'which rhino';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command = 'cscript';
        }
        exec($command, $output, $retVal);
        if ($retVal != 0){
            return array('retVal'=>false, 'message'=>$command . ' does not exist.');
        }
        if (!file_exists($filename)){
            return array('retVal'=>false, 'message'=>$filename . ' does not exist.');
        }
        return array('retVal'=>true, 'message'=>'');
    }

    /**
     * @dataProvider codeJsHintDataProvider
     */
    public function testCodeJsHint($filename)
    {
        $isTestRunnable = $this->_isTestRunnable($filename);
        if (!$isTestRunnable['retVal']){
            $this->markTestSkipped($isTestRunnable['message']);
        } else{
            $result = $this->_executeJsHint($filename);
            if (!$result) {
                $this->fail("Failed JSHint.");
            }
        }
    }

    /**
     * Build data provider array with command, js file name, and option
     * @return array
     */
    public function codeJsHintDataProvider()
    {
        self::setUpBeforeClass();
        $map = function($value)
        {
            return array($value);
        };
        return array_map($map, self::$_whiteListJsFiles);
    }

    /**
     * Returns cscript for windows and rhino for linux
     * @return string
     */
    protected function _getCommand()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'cscript ' . TESTS_JSHINT_PATH;
        } else {
            return 'rhino ' . TESTS_JSHINT_PATH;
        }
    }

    /**
     * Returns jshint option
     * @return string
     */
    protected function _getOption()
    {
        return TESTS_JSHINT_OPTIONS;
    }

    /**
     * Run jsHint againt js file; if failed output error to report file
     * @param $command - OS specific command
     * @param $filename - js file name with full path
     * @param $option - jsHint option
     * @return bool
     */
    protected function _executeJsHint($filename)
    {
        exec($this->_getCommand() . ' ' . $filename . ' ' . $this->_getOption(), $output, $retVal);
        $isOsWin = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if ($retVal == 0){
            return true;
        }
        $fh = fopen(self::$_reportFile, 'a');
        foreach ($output as $key => $line) {
            if (($isOsWin && $key < 2)){
                continue;
            }
            fwrite($fh, $line . PHP_EOL);
        }
        fwrite($fh, PHP_EOL);
        fclose($fh);
        return false;
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
        $map = function($value)
        {
            return trim($value) ? Utility_Files::init()->getPathToSource() . DIRECTORY_SEPARATOR .
                str_replace('/', DIRECTORY_SEPARATOR, trim($value)) : '';
        };
        return array_filter(array_map($map, $result), 'file_exists');
    }
}