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

    const CONFIGURATION_ERROR = 4;

    /**
     * Success log writer
     *
     * @var Writer\WriterInterface
     */
    protected $_successWriter;

    /**
     * Error log writer
     *
     * @var Writer\WriterInterface
     */
    protected $_errorWriter;

    /**
     * List of success log entries
     *
     * @var array
     */
    protected $_successEntries = array();

    /**
     * List of error entries
     *
     * @var array
     */
    protected $_errorEntries = array();

    /**
     * @param Writer\WriterInterface $successWriter
     * @param Writer\WriterInterface $errorWriter
     */
    public function __construct(Writer\WriterInterface $successWriter, Writer\WriterInterface $errorWriter)
    {
        $this->_successWriter = $successWriter;
        $this->_errorWriter = $errorWriter;
        $this->_successEntries[self::GENERATION_SUCCESS] = array();
        $this->_errorEntries = array(
            self::CONFIGURATION_ERROR => array(),
            self::GENERATION_ERROR => array(),
            self::COMPILATION_ERROR => array()
        );
    }

    /**
     * Add log message
     *
     * @param string $type
     * @param string $key
     * @param string $message
     * @return void
     */
    public function add($type, $key, $message = '')
    {
        if (array_key_exists($type, $this->_successEntries)) {
            $this->_successEntries[$type][$key][] = $message;
        } else {
            $this->_errorEntries[$type][$key][] = $message;
        }
    }

    /**
     * Write entries
     *
     * @return void
     */
    public function report()
    {
        $this->_successWriter->write($this->_successEntries);
        $this->_errorWriter->write($this->_errorEntries);
    }

    /**
     * Check whether error exists
     *
     * @return bool
     */
    public function hasError()
    {
        foreach ($this->_errorEntries as $data) {
            if (count($data)) {
                return true;
            }
        }
        return false;
    }
}
