<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Zend\Console\Console;
use Zend\Console\ColorInterface;

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
     * {@inheritdoc}
     */
    public function logSuccess($message)
    {
        $this->console->writeLine("[SUCCESS]" . ($message ? ": $message" : ''), ColorInterface::LIGHT_GREEN);
    }

    /**
     * {@inheritdoc}
     */
    public function logError(\Exception $e)
    {
        $this->console->writeLine("[ERROR]: " . $e, ColorInterface::LIGHT_RED);
    }

    /**
     * {@inheritdoc}
     */
    public function log($message)
    {
        $this->console->writeLine($message, ColorInterface::LIGHT_BLUE);
    }

    /**
     * {@inheritdoc}
     */
    public function logMeta($message)
    {
        $this->console->writeLine($message, ColorInterface::GRAY);
    }
}
