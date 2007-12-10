<?php

class Varien_Pear_Frontend extends PEAR_Frontend
{
    protected $_logStream = null;
    protected $_outStream = null;
    protected $_log = array();
    protected $_out = array();

    /**
     * Enter description here...
     *
     * @param string|resource $stream 'stdout' or open php stream
     */
    public function setLogStream($stream)
    {
        $this->_logStream = $stream;
        return $this;
    }

    public function log($msg, $append_crlf = true)
    {
        if (is_null($msg) || false===$msg or ''===$msg) {
            return;
        }

        if ($append_crlf) {
            $msg .= "\r\n";
        }

        $this->_log[] = $msg;

        if ('stdout'===$_logStream) {
            echo $msg;
        }
        elseif (is_resource($this->_logStream)) {
            fwrite($this->_logStream, $msg);
        }
    }

    public function outputData($data, $command = '_default')
    {
        $this->_out[] = array('output'=>$data, 'command'=>$command);
    }

    public function userConfirm()
    {

    }

    public function clear()
    {
        $this->_log = array();
        $this->_out = array();
    }

    public function getLog()
    {
        return $this->_log;
    }

    public function getLogText()
    {
        $text = '';
        foreach ($this->getLog() as $log) {
            $text .= $log;
        }
        return $text;
    }

    public function getOutput()
    {
        return $this->_out;
    }
}
