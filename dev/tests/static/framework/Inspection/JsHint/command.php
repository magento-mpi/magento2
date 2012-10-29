<?php
/**
 * PHP JsHint shell command
 */
class Inspection_JsHint_Command extends Inspection_CommandAbstract
{


    protected $_fileName;

    protected $_reportFile;

    public function __construct($fileName, $_reportFile)
    {
        $this->_fileName = $fileName;
        $this->_reportFile = $_reportFile;
    }

    /**
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
        return ltrim(str_replace('which', '', $this->_getHostScript())) . TESTS_JSHINT_PATH . ' ' . $this->_fileName . ' ' . TESTS_JSHINT_OPTIONS;
    }

    /**
     * @return string
     */

    protected function _isOsWin()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * Execute a shell command on the current environment and return its output or FALSE on failure
     *
     * @param string $shellCmd
     * @return string|false
     */

    protected function _execShellCmd($shellCmd)
    {
        exec($shellCmd, $output, $this->_lastExitCode);
        $this->_lastOutput = implode(PHP_EOL, $output);
        if ($this->_lastExitCode == 0) {
            return $this->_lastOutput;
        }
        if ($this->_isOsWin()) {
            $output = array_slice($output, 2);
        }
        $output[] = ''; //empty line to separate each file output
        file_put_contents($this->_reportFile, $this->_lastOutput, FILE_APPEND);
        return false;

    }


    public function run()
    {
        $shellCmd = $this->_buildShellCmd(null, null);
        $result = $this->_execShellCmd($shellCmd);
        $this->_generateLastRunMessage();
        return $result !== false;
    }

    /**
     * @throws Exception
     */
    public function canRun()
    {
        exec(trim($this->_getHostScript()), $output, $retVal);
        if ($retVal != 0) {
            throw new Exception($this->_getHostScript() . ' does not exist.');
        }
        if (!is_file(TESTS_JSHINT_PATH)) {
            throw new Exception(TESTS_JSHINT_PATH . ' does not exist.');
        }
        if (!file_exists($this->_fileName)) {
            throw new Exception($this->_fileName . ' does not exist.');
        }
    }

}