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
     * Allowed log types
     *
     * @var array
     */
    protected $_allowedTypes;

    /**
     * @param Writer\WriterInterface $writer
     * @param array $allowedTypes
     */
    public function __construct(Writer\WriterInterface $writer, $allowedTypes = array())
    {
        $this->_writer = $writer;
        $this->_allowedTypes = empty($allowedTypes)
            ? array(self::GENERATION_ERROR, self::COMPILATION_ERROR, self::GENERATION_SUCCESS)
            : $allowedTypes;
    }

    /**
     * Add log message
     *
     * @param string $type
     * @param string $key
     * @param string $message
     */
    public function add($type, $key, $message = '')
    {
        if (in_array($type, $this->_allowedTypes)) {
            $this->_entries[$type][$key][] = $message;
        }
    }

    /**
     * Write entries
     */
    public function report()
    {
        $this->_writer->write($this->_entries);
    }
}
