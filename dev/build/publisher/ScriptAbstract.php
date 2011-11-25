<?php
/**
 * {license_notice}
 *
 * @category   build
 * @package    publisher
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Runs repository commands in the folder of a repository
 *
 */
abstract class ScriptAbstract {

    /**
     * Path of repository
     *
     * @var string
     */
    public $_repositoryPath;

    /**
     * Verbose mode flag
     *
     * @var bool
     */
    public $_verbose = false;

    /**
     * Set path of a repository
     *
     * @param string $path
     * @return RepositoryDriver
     */
    public function setRepositoryPath($path)
    {
        $this->_repositoryPath = $path;
        return $this;
    }

    /**
     * Set path of a repository
     *
     * @param string $value
     * @return RepositoryDriver
     */
    public function setVerbose($value)
    {
        $this->_verbose = $value;
        return $this;
    }

    /**
     * Run script
     *
     * @abstract
     * @return void
     */
    abstract public function run();

    /**
     * Call the command as a git subcommand for the current repository
     *
     * @param string $command
     * @param bool $errorMessage
     * @return array|bool
     */
    protected function _callGitCommand($command, $errorMessage = false)
    {
        $output = array();
        $exitCode = $this->_execCmd(
            'git --git-dir ' . $this->_repositoryPath . DIRECTORY_SEPARATOR . '.git '
                . '--work-tree ' . $this->_repositoryPath . ' '
                . $command, $output);

        if ($exitCode !== 0) {
            if ($errorMessage) {
                $this->_throwException($errorMessage);
            }
            return false;
        }

        return $output;
    }

    /**
     * Exec command
     *
     * @param string $cmd
     * @param array $output
     * @return int
     */
    protected function _execCmd($cmd, &$output = array())
    {
        $exitCode = 0;
        if ($this->_verbose) {
            echo $cmd . "\n";
        }
        exec($cmd, $output, $exitCode);
        if ($this->_verbose) {
            if (count($output) > 0) {
                echo implode("\n", $output) . "\n";
            }
        }
        return $exitCode;
    }

    /**
     * Throw exception with message and complements the message with path of repository.
     *
     * @throws Exception
     * @param string $message
     * @return void
     */
    protected function _throwException($message)
    {
        throw new Exception("Repository '{$this->_repositoryPath}'. " . $message);
    }
}
