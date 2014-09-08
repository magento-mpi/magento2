<?php
/**
 * Created by PhpStorm.
 * User: japatel
 * Date: 9/8/14
 * Time: 2:46 PM
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
     * @param string $moduleName
     * @return void
     */
    public function logSuccess($moduleName)
    {
        $this->console->writeLine("[SUCCESS]: $moduleName is installed");
    }

    /**
     * Logs error message
     *
     * @param \Exception $e
     * @return void
     */
    public function logError(\Exception $e)
    {
        $this->console->writeLine("[ERROR]: " . $e);
    }

    /**
     * Logs message to log writer
     *
     * @param string $message
     * @return void
     */
    public function log($message)
    {
        $this->console->writeLine($message);
    }
}