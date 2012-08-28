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
class Js_LiveCodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_reportDir = '';

    /**
     * @var array
     */
    protected static $_whiteListJsFiles = array();

    /**
     * @var array
     */
    protected static $_blackListJsFiles = array();



    protected static function _scanFileNameRecursivly($path = '', &$name = array())
    {
        if (is_file($path)){
            return array($path);
        }
        $path = $path == '' ? dirname(__FILE__) : $path;
        $lists = @scandir($path);
        if (!empty($lists)) {
            foreach ($lists as $f) {
                if (is_dir($path . DIRECTORY_SEPARATOR . $f) && $f != ".." && $f != ".") {
                    self::_scanFileNameRecursivly($path . DIRECTORY_SEPARATOR . $f, &$name);
                } else if (strlen($f) > 3 && substr($f, -3) == '.js') {
                    $name[] = $path . DIRECTORY_SEPARATOR . $f;
                }
            }
        }
        return $name;
    }

    public static function setUpBeforeClass()
    {
        self::$_reportDir = Utility_Files::init()->getPathToSource() . '/dev/tests/static/report';
        if (!is_dir(self::$_reportDir)) {
            mkdir(self::$_reportDir, 0777);
        }
        $whiteList = self::_readLists(__DIR__ . '/_files/whitelist/*.txt');
        $blackList = self::_readLists(__DIR__ . '/_files/blacklist/*.txt');
        foreach ($blackList as $listFiles){
            self::$_blackListJsFiles = array_merge(self::$_blackListJsFiles, self::_scanFileNameRecursivly($listFiles));
        }
        foreach ($whiteList as $listFiles){
            self::$_whiteListJsFiles = array_merge(self::$_whiteListJsFiles, self::_scanFileNameRecursivly($listFiles));
        }
        $blackListJsFiles = self::$_blackListJsFiles;
        $filter = function($value) use($blackListJsFiles){
            return !in_array($value, $blackListJsFiles);
        };
        self::$_whiteListJsFiles = array_filter(self::$_whiteListJsFiles, $filter);
    }

    /**
     * @dataProvider provider
     */
    public function testCodeJsHint($command, $filename, $option)
    {
        $result = $this->executeJsHint($command, $filename, $option);
        if (!$result) {
            $this->fail("testJsHint failed with argument " . $filename);
        }
    }

    public function provider()
    {
        self::setUpBeforeClass();
        $command = $this->getCommand();
        $option = $this->getOption();
        $map = function($value) use ($command, $option)
        {
            return array($command, $value, $option);
        };
        return array_map($map, self::$_whiteListJsFiles);
    }

    protected function getCommand(){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
            return 'cscript c:\git_workspace\required\jshint\env\wsh.js';
        }
    }

    protected function getOption(){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
            return '/global:document:true,mage:true /jquery:true';
        }
    }

    protected function executeJsHint($command, $filename, $option)
    {
        exec($command . ' ' . $filename . ' ' . $option, $output);
        if (count($output) == 3) {
            return true;
        }
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
