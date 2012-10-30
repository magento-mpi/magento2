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
/**
 * PHP JsHint shell command
 */
class Inspection_JsHint_Command extends Inspection_CommandAbstract
{

    /**
     * @var string
     */
    protected $_fileName;
    /**
     * @var string
     */
    protected $_reportFile;

    /**
     * Constructor
     *
     * @param string $fileName js file name
     * @param string $reportFile Destination file to write JsHint report to
     */
    public function __construct($fileName, $reportFile)
    {
        $this->_fileName = $fileName;
        $this->_reportFile = $reportFile;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }

    /**
     * Unable to get JsHint version frm command line
     * @return string
     */
    protected function _buildVersionShellCmd()
    {
        return null;
    }

    /**
     * @return string
     */
    protected function _getHostScript()
    {
        return ($this->_isOsWin() === true) ? 'cscript ' : 'which rhino ';
    }

    /**
     * @param array $whiteList
     * @param array $blackList
     * @return string
     */
    protected function _buildShellCmd($whiteList, $blackList)
    {
        return ltrim(str_replace('which', '', $this->_getHostScript())) . ' '
            . $this->_getJsHintPath() . ' '
            . $this->getFileName() . ' '
            . $this->_getJsHintOptions();
    }

    /**
     * @return boolean
     */
    protected function _isOsWin()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * @return string
     */
    protected function _getJsHintOptions()
    {
        $jsHintOptionsArray = array('eqnull' => 'true', 'browser' => 'true', 'jquery' => 'true');
        $jsHintOptions = null;
        if ($this->_isOsWin()) {
            foreach ($jsHintOptionsArray as $key => $value) {
                $jsHintOptions .= "/$key:$value ";
            }
        } else {
            foreach ($jsHintOptionsArray as $key => $value) {
                $jsHintOptions .= "$key=$value,";
            }
        }
        return trim(rtrim($jsHintOptions, ","));
    }

    /**
     * Execute a shell command on the current environment and return its output or FALSE on failure
     *
     * @param string $shellCmd
     * @return string|false
     */
    protected function _execShellCmd($shellCmd)
    {
        $retArray = $this->_executeCommand($shellCmd);
        $this->_lastOutput = implode(PHP_EOL, $retArray[0]);
        $this->_lastExitCode = $retArray[1];
        if ($this->_lastExitCode == 0) {
            return $this->_lastOutput;
        }
        if ($this->_isOsWin()) {
            $output = array_slice($retArray[0], 2);
        }
        $output[] = ''; //empty line to separate each file output
        file_put_contents($this->_reportFile, $this->_lastOutput, FILE_APPEND);
        return false;

    }

    /**
     * @return string
     */
    protected function _getJsHintPath()
    {
        return TESTS_JSHINT_PATH;
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function _fileExists($fileName)
    {
        return is_file($fileName);
    }

    /**
     * @param string $cmd
     * @return array
     */
    protected function _executeCommand($cmd)
    {
        exec(trim($cmd), $output, $retVal);
        return array($output, $retVal);
    }

    /**
     * @throws Exception
     * @return boolean
     */
    public function canRun()
    {
        $retArray = $this->_executeCommand($this->_getHostScript());
        if ($retArray[1] != 0) {
            throw new Exception($this->_getHostScript() . ' does not exist.');
        }
        if (!$this->_fileExists($this->_getJsHintPath())) {
            throw new Exception($this->_getJsHintPath() . ' does not exist.');
        }
        if (!$this->_fileExists($this->getFileName())) {
            throw new Exception($this->getFileName() . ' does not exist.');
        }
        return true;
    }

}