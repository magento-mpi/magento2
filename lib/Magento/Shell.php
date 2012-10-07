<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shell command line wrapper encapsulates command execution and arguments escaping
 */
class Magento_Shell
{
    /**
     * Verbosity of command execution - whether command output is printed to the standard output or not
     *
     * @var bool
     */
    protected $_isVerbose;

    /**
     * Constructor
     *
     * @param bool $isVerbose Whether command output is printed to the standard output or not
     */
    public function __construct($isVerbose = false)
    {
        $this->_isVerbose = $isVerbose;
    }

    /**
     * Set verbosity
     *
     * @param bool $isVerbose
     * @return Magento_Shell
     */
    public function setVerbose($isVerbose)
    {
        $this->_isVerbose = $isVerbose;
        return $this;
    }

    /**
     * Get verbosity
     *
     * @return bool
     */
    public function getVerbose()
    {
        return $this->_isVerbose;
    }

    /**
     * Execute a command through the command line, passing properly escaped arguments, and return its output
     *
     * @param string $command Command with optional argument markers '%s'
     * @param array $arguments Argument values to substitute markers with
     * @param string &$fullOutput A string to dump all actual output
     * @return array raw output from exec() PHP-function (the second argument)
     * @throws Magento_Exception if exit code is other than zero
     */
    public function execute($command, array $arguments = array(), &$fullOutput = '')
    {
        $arguments = array_map('escapeshellarg', $arguments);
        $rawCommand = vsprintf("{$command} 2>&1", $arguments); // Output errors to STDOUT instead of STDERR
        $output = $rawCommand . PHP_EOL;
        $fullOutput .= $output;
        if ($this->_isVerbose) {
            echo $output;
        }
        exec($rawCommand, $rawOutput, $exitCode);
        $rawOutputStr = implode(PHP_EOL, $rawOutput);
        $output = $rawOutputStr . PHP_EOL . PHP_EOL;
        $fullOutput .= $output;
        if ($this->_isVerbose) {
            echo $output;
        }
        if ($exitCode) {
            $commandError = new Exception($rawOutputStr, $exitCode);
            throw new Magento_Exception("Command `$command` returned non-zero exit code.", 0, $commandError);
        }
        return $rawOutput;
    }
}
