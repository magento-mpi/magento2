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
     * @static return all files under a path
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

    /**
     * @static setup report file, black list and white list
     *
     */
    public static function setUpBeforeClass()
    {
        $reportDir = Utility_Files::init()->getPathToSource() . DIRECTORY_SEPARATOR . 'dev' . DIRECTORY_SEPARATOR .
            'tests' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'report';
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0777);
        }
        self::$_reportFile = $reportDir . DIRECTORY_SEPARATOR . 'js_report.txt';
        @unlink(self::$_reportFile);
        $whiteList = self::_readLists(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR .
            'whitelist' . DIRECTORY_SEPARATOR . '*.txt');
        $blackList = self::_readLists(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR .
            'blacklist' . DIRECTORY_SEPARATOR . '*.txt');
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

    /**
     * @dataProvider codeJsHintDataProvider
     */
    public function testCodeJsHint($filename)
    {
        $result = $this->_executeJsHint($filename);
        if (!$result) {
            $this->fail("failed jsHint.");
        }
    }

    /**
     * build data provider array with command, js file name, and option
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
     * returns cscript for windows and rhino for linux
     * @return string
     */
    protected function _getCommand()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'cscript ' . TESTS_JSHINT_WIN_PATH;
        } else {
            return 'rhino ' . TESTS_JSHINT_LINUX_PATH;
        }
    }

    /**
     * returns jshint option
     * @return string
     */
    protected function _getOption()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return TESTS_JSHINT_WIN_OPTIONS;
        } else {
            return TESTS_JSHINT_LINUX_OPTIONS;
        }
    }

    /**
     * run jsHint againt js file; if failed output error to report file
     * @param $command - OS specific command
     * @param $filename - js file name with full path
     * @param $option - jsHint option
     * @return bool
     */
    protected function _executeJsHint($filename)
    {
        exec($this->_getCommand() . ' ' . $filename . ' ' . $this->_getOption(), $output);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if (count($output) == 3) {
                return true;
            }
            $fh = fopen(self::$_reportFile, 'a');
            foreach ($output as $key => $line) {
                fwrite($fh, $line . PHP_EOL);
            }
            fwrite($fh, PHP_EOL);
            fclose($fh);

        } else {
            if (count($output) === 0) {
                return true;
            } else {
                $fh = fopen(self::$_reportFile, 'a');
                foreach ($output as $key => $line) {
                    fwrite($fh, $line . PHP_EOL);
                }
                fwrite($fh, PHP_EOL);
                fclose($fh);
                return false;
            }
        }
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
