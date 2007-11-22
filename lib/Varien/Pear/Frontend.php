<?php

class Varien_Pear_Frontend extends PEAR_Frontend
{
    protected $_log = array();
    protected $_out = array();

    public function log($msg)
    {
        $this->_log[] = $msg;
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
            if ($log[0]!=='.') {
                $text .= "\n";
            }
        }
        return $text;
    }

    public function getOutput()
    {
        return $this->_out;
    }
}
