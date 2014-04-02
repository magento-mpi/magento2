<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

/**
 * Shell command line wrapper encapsulates command execution and arguments escaping
 */
class Shell
{
    /**
     * Logger instance
     *
     * @var \Zend_Log
     */
    protected $_logger;

    /**
     * Operation system info
     *
     * @var OsInfo
     */
    protected $_osInfo;

    /**
     * @param \Zend_Log $logger Logger instance to be used to log commands and their output
     */
    public function __construct(\Zend_Log $logger = null)
    {
        $this->_logger = $logger;
    }

    /**
     * Execute a command through the command line, passing properly escaped arguments, and return its output
     *
     * @param string $command Command with optional argument markers '%s'
     * @param [] $arguments Argument values to substitute markers with
     * @return string Output of an executed command
     * @throws \Magento\Exception If a command returns non-zero exit code
     */
    public function execute($command, array $arguments = array())
    {
        $arguments = array_map('escapeshellarg', $arguments);
        $command = preg_replace('/\s?\||$/', ' 2>&1$0', $command);
        // Output errors to STDOUT instead of STDERR
        $command = vsprintf($command, $arguments);
        $this->log($command);
        exec($command, $output, $exitCode);
        $output = implode(PHP_EOL, $output);
        $this->log($output);
        if ($exitCode) {
            $commandError = new \Exception($output, $exitCode);
            throw new \Magento\Exception("Command `{$command}` returned non-zero exit code.", 0, $commandError);
        }
        return $output;
    }

    /**
     * Log a message, if a logger is specified
     *
     * @param string $message
     * @return void
     */
    protected function log($message)
    {
        if ($this->_logger) {
            $this->_logger->log($message, \Zend_Log::INFO);
        }
    }
}
