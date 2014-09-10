<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Zend\Console\Console;
/**
 * Console Logger
 *
 * @package Magento\Setup\Model
 */
class ConsoleLogger implements LoggerInterface
{

    /**
     * Console
     *
     * @var \Zend\Console\Adapter\AdapterInterface
     */
    protected $console;

    /**
     * Default Constructor
     */
    public function __construct()
    {
        $this->console = Console::getInstance();
    }

    /**
     * Logs success message
     *
     * @param string $message
     * @return void
     */
    public function logSuccess($message)
    {
        $this->console->writeLine("[SUCCESS]" . ($message ? ": $message" : ''), 11);
    }

    /**
     * Logs error message
     *
     * @param \Exception $e
     * @return void
     */
    public function logError(\Exception $e)
    {
        $this->console->writeLine("[ERROR]: " . $e, 10);
    }

    /**
     * Logs message to log writer
     *
     * @param string $message
     * @return void
     */
    public function log($message)
    {
        $this->console->writeLine($message, 13);
    }
}
