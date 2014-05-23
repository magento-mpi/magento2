<?php

namespace Magento\Composer\Log;

class Log {

    private $_logwriter = null;
    private $_debugwriter = null;

    public function __construct(\Magento\Composer\Log\Writer $logWriter = null, \Magento\Composer\Log\Writer $debugWriter = null){
        if(isset($logWriter)){
            $this->_logwriter = $logWriter;
        } else {
            $this->_logwriter = new \Magento\Composer\Log\Writer\DefaultWriter();

        }
        if(isset($debugWriter)){
            $this->_debugwriter = $debugWriter;
        } else {
            $this->_debugwriter = new \Magento\Composer\Log\Writer\DefaultWriter();
        }
    }

    public function error($args){
        $args = func_get_args();
        $this->_logwriter->write($args);
    }

    public function log($args){
        $args = func_get_args();
        $this->_logwriter->write($args);
    }

    public function debug($args){
        $args = func_get_args();
        $this->_debugwriter->write($args);
    }
}