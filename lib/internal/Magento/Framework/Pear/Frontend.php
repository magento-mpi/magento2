<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Framework\Pear;

class Frontend
{
    /**
     * @var string|resource
     */
    protected $_logStream = null;

    /**
     * @var null
     */
    protected $_outStream = null;

    /**
     * @var string[]
     */
    protected $_log = [];

    /**
     * @var array
     */
    protected $_out = [];

    /**
     * Enter description here...
     *
     * @param string|resource $stream 'stdout' or open php stream
     * @return $this
     */
    public function setLogStream($stream)
    {
        $this->_logStream = $stream;
        return $this;
    }

    /**
     * @return string|resource
     */
    public function getLogStream()
    {
        return $this->_logStream;
    }

    /**
     * @param string $msg
     * @param bool $append_crlf
     * @return void
     */
    public function log($msg, $append_crlf = true)
    {
        if (is_null($msg) || false === $msg or '' === $msg) {
            return;
        }

        if ($append_crlf) {
            $msg .= "\r\n";
        }

        $this->_log[] = $msg;

        if ('stdout' === $this->_logStream) {
            if ($msg === '.') {
                echo ' ';
            }
            echo $msg;
        } elseif (is_resource($this->_logStream)) {
            fwrite($this->_logStream, $msg);
        }
    }

    /**
     * @param string|array $data
     * @param string $command
     * @return void
     */
    public function outputData($data, $command = '_default')
    {
        $this->_out[] = ['output' => $data, 'command' => $command];

        if ('stdout' === $this->_logStream) {
            if (is_string($data)) {
                echo $data . "\r\n";
            } elseif (is_array($data) && !empty($data['message']) && is_string($data['message'])) {
                echo $data['message'] . "\r\n";
            } elseif (is_array($data) && !empty($data['data']) && is_string($data['data'])) {
                echo $data['data'] . "\r\n";
            } else {
                print_r($data);
            }
        }
    }

    /**
     * @return void
     */
    public function userConfirm()
    {
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->_log = [];
        $this->_out = [];
    }

    /**
     * @return string[]
     */
    public function getLog()
    {
        return $this->_log;
    }

    /**
     * @return string
     */
    public function getLogText()
    {
        $text = '';
        foreach ($this->getLog() as $log) {
            $text .= $log;
        }
        return $text;
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return $this->_out;
    }
}
