<?php

namespace Magento\Composer\Log\Writer;

class DefaultWriter implements \Magento\Composer\Log\Writer{

    public function write($args){
        $var  = array_shift($args);
        $date = date('m/d/y h:i:s', time());
        $s = vsprintf("[" . $date ."] - ". $var . "\n", $args);
        fwrite(STDOUT, $s );
    }
}