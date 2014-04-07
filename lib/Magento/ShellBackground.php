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

use Magento\Shell\CommandRendererInterface;
use Magento\Webapi\Exception;

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
    protected $logger;

    /**
     * Operation system info
     *
     * @var OsInfo
     */
    protected $osInfo;

    /**
     * @var CommandRendererInterface
     */
    private $commandRenderer;

    /**
     * @param CommandRendererInterface $commandRenderer
     * @param OsInfo $osInfo
     * @param \Zend_Log $logger Logger instance to be used to log commands and their output
     */
    public function __construct(
        CommandRendererInterface $commandRenderer,
        OsInfo $osInfo,
        \Zend_Log $logger = null
    ) {
        $this->logger = $logger;
        $this->osInfo = $osInfo;
        $this->commandRenderer = $commandRenderer;
    }

    /**
     * Execute a command through the command line, passing properly escaped arguments in background
     *
     * @param string $command Command with optional argument markers '%s'
     * @param string[] $arguments Argument values to substitute markers with
     * @throws \Magento\Exception If a command returns non-zero exit code
     * @return void
     */
    public function execute($command, array $arguments = array())
    {
        $command = $this->commandRenderer->render($command, $arguments);

        if ($this->osInfo->isWindows()) {
            $command = 'start /B "magento background task" ' . $command;
        } else {
            $command .= ' > /dev/null 2>&1 &';
        }
        $this->log($command);
        $handle = popen($command, 'r');
        if ($handle === false) {
            throw new \Magento\Exception("Command `{$command}` returned non-zero exit code.");
        }
        pclose($handle);
    }

    /**
     * Log a message, if a logger is specified
     *
     * @param string $message
     * @return void
     */
    protected function log($message)
    {
        if ($this->logger) {
            $this->logger->log($message, \Zend_Log::INFO);
        }
    }
}
