<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Di\Compiler\Log;
use Magento\Tools\Di\Compiler\Log\Writer;

class Log
{
    const GENERATION_ERROR = 1;
    const GENERATION_SUCCESS = 2;
    const COMPILATION_ERROR = 3;

    /**
     * Log writer
     *
     * @var Writer\WriterInterface
     */
    protected $_writer;

    /**
     * List of log entries
     *
     * @var array
     */
    protected $_entries = array();

    /**
     * @param Writer\WriterInterface $writer
     */
    public function __construct(Writer\WriterInterface $writer)
    {
        $this->_writer = $writer;
    }

    /**
     * @param string $type
     * @param string $className
     * @param string $message
     */
    public function log($type, $key, $message = '')
    {
        $this->_entries[$type][$key][] = $message;
    }

    /**
     * Write entries
     */
    public function report()
    {
        $this->_writer->write($this->_entries);
    }
}
