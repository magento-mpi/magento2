<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2008, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2008 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id: PhptTestCase.php 4219 2008-12-10 08:56:57Z sb $
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.1.4
 */

if (PHPUnit_Util_Filesystem::fileExistsInIncludePath('PEAR/RunTest.php')) {
    $currentErrorReporting = error_reporting(E_ERROR | E_WARNING | E_PARSE);
    PHPUnit_Util_Filesystem::collectStart();
    require_once 'PEAR/RunTest.php';
    error_reporting($currentErrorReporting);

    foreach (PHPUnit_Util_Filesystem::collectEnd() as $blacklistedFile) {
        PHPUnit_Util_Filter::addFileToFilter($blacklistedFile, 'PHPUNIT');
    }
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/PhptTestCase/Logger.php';
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * Wrapper to run .phpt test cases.
 *
 * @category   Testing
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2008 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: 3.3.9
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.1.4
 */
class PHPUnit_Extensions_PhptTestCase implements PHPUnit_Framework_Test, PHPUnit_Framework_SelfDescribing
{
    /**
     * The filename of the .phpt file.
     *
     * @var    string
     */
    protected $filename;

    /**
     * Options for PEAR_RunTest.
     *
     * @var    array
     */
    protected $options = array();

    /**
     * Constructs a test case with the given filename.
     *
     * @param  string $filename
     * @param  array  $options Array with ini settings for the php instance run,
     *                         key being the name if the setting, value the ini value.
     */
    public function __construct($filename, $options = array())
    {
        if (!is_string($filename)) {
            throw new InvalidArgumentException;
        }

        if (!is_file($filename)) {
            throw new RuntimeException(
              sprintf(
                'File "%s" does not exist.',
                $filename
              )
            );
        }

        if (!is_array($options)) {
            throw new InvalidArgumentException;
        }

        $this->filename = $filename;
        $this->options  = $options;
    }

    /**
     * Counts the number of test cases executed by run(TestResult result).
     *
     * @return integer
     */
    public function count()
    {
        return 1;
    }

    /**
     * Runs a test and collects its result in a TestResult instance.
     *
     * @param  PHPUnit_Framework_TestResult $result
     * @param  array $options Array with ini settings for the php instance run,
     *                        key being the name if the setting, value the ini value.
     * @return PHPUnit_Framework_TestResult
     */
    public function run(PHPUnit_Framework_TestResult $result = NULL, $options = array())
    {
        if (!class_exists('PEAR_RunTest', FALSE)) {
            throw new RuntimeException('Class PEAR_RunTest not found.');
        }

        if (isset($GLOBALS['_PEAR_destructor_object_list']) &&
            is_array($GLOBALS['_PEAR_destructor_object_list']) &&
            !empty($GLOBALS['_PEAR_destructor_object_list'])) {
            $pearDestructorObjectListCount = count($GLOBALS['_PEAR_destructor_object_list']);
        } else {
            $pearDestructorObjectListCount = 0;
        }

        if ($result === NULL) {
            $result = new PHPUnit_Framework_TestResult;
        }

        if (!is_array($options)) {
            throw new InvalidArgumentException;
        }

        $coverage = $result->getCollectCodeCoverageInformation();
        $options  = array_merge($options, $this->options);

        if ($coverage) {
            $options['coverage'] = TRUE;
        } else {
            $options['coverage'] = FALSE;
        }

        $currentErrorReporting = error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $runner                = new PEAR_RunTest(new PHPUnit_Extensions_PhptTestCase_Logger, $options);

        if ($coverage) {
            $runner->xdebug_loaded = TRUE;
        } else {
            $runner->xdebug_loaded = FALSE;
        }

        $result->startTest($this);

        PHPUnit_Util_Timer::start();
        $buffer       = $runner->run($this->filename, $options);
        $time         = PHPUnit_Util_Timer::stop();
        error_reporting($currentErrorReporting);
        $base         = basename($this->filename);
        $path         = dirname($this->filename);
        $coverageFile = $path . DIRECTORY_SEPARATOR . str_replace('.phpt', '.xdebug', $base);
        $diffFile     = $path . DIRECTORY_SEPARATOR . str_replace('.phpt', '.diff', $base);
        $expFile      = $path . DIRECTORY_SEPARATOR . str_replace('.phpt', '.exp', $base);
        $logFile      = $path . DIRECTORY_SEPARATOR . str_replace('.phpt', '.log', $base);
        $outFile      = $path . DIRECTORY_SEPARATOR . str_replace('.phpt', '.out', $base);
        $phpFile      = $path . DIRECTORY_SEPARATOR . str_replace('.phpt', '.php', $base);

        if (is_object($buffer) && $buffer instanceof PEAR_Error) {
            $result->addError(
              $this,
              new RuntimeException($buffer->getMessage()),
              $time
            );
        }

        else if ($buffer == 'SKIPPED') {
            $result->addFailure($this, new PHPUnit_Framework_SkippedTestError, 0);
        }

        else if ($buffer != 'PASSED') {
            $result->addFailure(
              $this,
              PHPUnit_Framework_ComparisonFailure::diffEqual(
                file_get_contents($expFile),
                file_get_contents($outFile)
              ),
              $time
            );
        }

        foreach (array($diffFile, $expFile, $logFile, $phpFile, $outFile) as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        if ($coverage && file_exists($coverageFile)) {
            eval('$coverageData = ' . file_get_contents($coverageFile) . ';');
            unset($coverageData[$phpFile]);

            $result->appendCodeCoverageInformation($this, $coverageData);
            unlink($coverageFile);
        }

        $result->endTest($this, $time);

        // Do not invoke PEAR's destructor mechanism for PHP 4
        // as it raises an E_STRICT.
        if ($pearDestructorObjectListCount == 0) {
            unset($GLOBALS['_PEAR_destructor_object_list']);
        } else {
            $count = count($GLOBALS['_PEAR_destructor_object_list']) - $pearDestructorObjectListCount;

            for ($i = 0; $i < $count; $i++) {
                array_pop($GLOBALS['_PEAR_destructor_object_list']);
            }
        }

        return $result;
    }

    /**
     * Returns the name of the test case.
     *
     * @return string
     */
    public function getName()
    {
        return $this->toString();
    }

    /**
     * Returns a string representation of the test case.
     *
     * @return string
     */
    public function toString()
    {
        return $this->filename;
    }
}
?>
