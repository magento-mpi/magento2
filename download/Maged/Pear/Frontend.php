<?php

class Maged_Pear_Frontend extends PEAR_Frontend
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

    public function getLogStream()
    {
        return $this->_logStream;
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

        if ('stdout'===$this->_logStream) {
            echo $msg;
        }
        elseif (is_resource($this->_logStream)) {
            fwrite($this->_logStream, $msg);
        }
    }

    public function outputData($data, $command = '_default')
    {
        $this->_out[] = array('output'=>$data, 'command'=>$command);

        if ('stdout'===$this->_logStream) {
            if (is_string($data)) {
                echo $data."\r\n";
            } elseif (is_array($data)) {
                if (!empty($data['message']) && is_string($data['message'])) {
                    echo $data['message']."\r\n";
                } elseif (!empty($data['data'])) {
                    if (is_string($data['data'])) {
                        echo $data['data']."\r\n";
                    } elseif (!empty($data['headline']) && is_array($data['data'])) {
                        echo $data['headline']."\r\n";
                        foreach ($data['data'] as $msg) {
                            echo $msg[0]."\r\n";
                        }
                    } else {
                        print_r($data);
                    }
                } else {
                    print_r($data);
                }
            } else {
                print_r($data);
            }
        }
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

    public function processOutputData($data)
    {
        if (isset($data['headline'])) {
            $rows = array();
            foreach ($data['data'] as $i=>$fields) {
                foreach ($fields as $f=>$v) {
                    $rows[$i][$data['headline'][$f]] = $v;
                }
            }
            return $rows;
        } else {
            $data = $data['data'];
            while (is_array($data)) {
                $data = $data[0];
            }
            return $data;
        }
    }
}

