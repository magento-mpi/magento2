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
class ShellBackground implements ShellInterface
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
     * @param OsInfo $osInfo
     * @param \Zend_Log $logger Logger instance to be used to log commands and their output
     */
    public function __construct(OsInfo $osInfo, \Zend_Log $logger = null)
    {
        $this->_logger = $logger;
        $this->_osInfo = $osInfo;
    }

    /**
     * Execute a command through the command line, passing properly escaped arguments in background
     *
     * @param string $command Command with optional argument markers '%s'
     * @param array $arguments Argument values to substitute markers with
     */
    public function execute($command, array $arguments = array())
    {
        $arguments = array_map('escapeshellarg', $arguments);
        $command = preg_replace('/\s?\||$/', ' 2>&1$0', $command);
        $command = vsprintf($command, $arguments);

        if ($this->_osInfo->isWindows()) {
            $command = 'start /B "magento background task" ' . $command;
        } else {
            $command .= ' > /dev/null 2>1 &';
        }
        $this->log($command);
        pclose(popen($command, 'r'));
    }

    /**
     * Log a message, if a logger is specified
     *
     * @param string $message
     */
    protected function log($message)
    {
        if ($this->_logger) {
            $this->_logger->log($message, \Zend_Log::INFO);
        }
    }
}
