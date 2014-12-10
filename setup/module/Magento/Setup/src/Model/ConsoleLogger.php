<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Setup\Model;

use Zend\Console\ColorInterface;
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
    public function log($message, $addEol = true)
    {
        if ($addEol) {
            $this->console->writeLine($message, ColorInterface::LIGHT_BLUE);
        } else {
            $this->console->write($message, ColorInterface::LIGHT_BLUE);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function logMeta($message)
    {
        $this->console->writeLine($message, ColorInterface::GRAY);
    }
}
