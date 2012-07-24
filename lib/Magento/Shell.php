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
     * @return string
     * @throws Magento_Exception
     */
    public function execute($command, array $arguments = array())
    {
        $arguments = array_map('escapeshellarg', $arguments);
        $command = vsprintf($command, $arguments);
        /* Output errors to STDOUT instead of STDERR */
        exec("$command 2>&1", $output, $exitCode);
        $output = implode(PHP_EOL, $output);
        if ($this->_isVerbose) {
            echo $output . PHP_EOL;
        }
        if ($exitCode) {
            $commandError = new Exception($output, $exitCode);
            throw new Magento_Exception("Command `$command` returned non-zero exit code.", 0, $commandError);
        }
        return $output;
    }
}
