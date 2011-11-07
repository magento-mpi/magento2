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
 * Abstract shell command for the static code inspection
 */
abstract class Inspection_CommandAbstract
{
    /**
     * @var string
     */
    protected $_reportFile;

    /**
     * @var array
     */
    protected $_whiteList;

    /**
     * @var array
     */
    protected $_blackList;

    /**
     * Constructor
     *
     * @param string $reportFile Destination file to write inspection report to
     * @param array $whiteList Files/folders to be inspected
     * @param array $blackList Files/folders to be excluded from the inspection
     */
    public function __construct($reportFile, array $whiteList, array $blackList = array())
    {
        $this->_reportFile = $reportFile;
        $this->_whiteList = $whiteList;
        $this->_blackList = $blackList;
    }

    /**
     * Build and execute the shell command
     *
     * @return bool
     */
    public function run()
    {
        $shellCmd = $this->_buildShellCmd();
        return $this->_execShellCmd($shellCmd);
    }

    /**
     * Whether the command can be ran on the current environment
     *
     * @return bool
     */
    public function canRun()
    {
        return $this->_execShellCmd($this->_buildVersionShellCmd());
    }

    /**
     * Retrieve the shell command version
     *
     * @return string|null
     */
    public function getVersion()
    {
        if (!$this->_execShellCmd($this->_buildVersionShellCmd(), $versionOutput)) {
            return null;
        }
        $versionOutput = implode("\n", $versionOutput);
        return (preg_match('/[^\d]*([^\s]+)/', $versionOutput, $matches) ? $matches[1] : $versionOutput);
    }

    /**
     * Build the shell command that outputs the version
     *
     * @return string
     */
    abstract protected function _buildVersionShellCmd();

    /**
     * Build the valid shell command
     *
     * @return string
     */
    abstract protected function _buildShellCmd();

    /**
     * Execute the shell command on the current environment
     *
     * @param string $shellCmd
     * @param array $output
     * @return bool
     */
    protected function _execShellCmd($shellCmd, array &$output = null)
    {
        $output = array();
        exec($shellCmd . ' 2>&1', $output, $exitCode);
        return ($exitCode === 0);
    }
}
