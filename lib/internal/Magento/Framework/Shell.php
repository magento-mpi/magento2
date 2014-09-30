<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework;

use Magento\Framework\Shell\CommandRendererInterface;
use Magento\Framework\Exception;
/**
 * Shell command line wrapper encapsulates command execution and arguments escaping
 */
class Shell implements ShellInterface
{
    /**
     * Logger instance
     *
     * @var \Zend_Log
     */
    protected $logger;

    /**
     * @var CommandRendererInterface
     */
    private $commandRenderer;

    /**
     * @param CommandRendererInterface $commandRenderer
     * @param \Zend_Log $logger Logger instance to be used to log commands and their output
     */
    public function __construct(
        CommandRendererInterface $commandRenderer,
        \Zend_Log $logger = null
    ) {
        $this->logger = $logger;
        $this->commandRenderer = $commandRenderer;
    }

    /**
     * Execute a command through the command line, passing properly escaped arguments, and return its output
     *
     * @param string $command Command with optional argument markers '%s'
     * @param string[] $arguments Argument values to substitute markers with
     * @return string Output of an executed command
     * @throws \Magento\Framework\Exception If a command returns non-zero exit code
     */
    public function execute($command, array $arguments = array())
    {
        $command = $this->commandRenderer->render($command, $arguments);
        $this->log($command);

        $disabled = explode(',', ini_get('disable_functions'));
        if (in_array('exec', $disabled)) {
            throw new Exception("exec function is disabled.");
        }

        exec($command, $output, $exitCode);
        $output = implode(PHP_EOL, $output);
        $this->log($output);
        if ($exitCode) {
            $commandError = new \Exception($output, $exitCode);
            throw new Exception("Command `{$command}` returned non-zero exit code.", 0, $commandError);
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
        if ($this->logger) {
            $this->logger->log($message, \Zend_Log::INFO);
        }
    }
}
