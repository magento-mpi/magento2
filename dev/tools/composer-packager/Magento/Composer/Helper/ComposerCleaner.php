<?php

namespace Magento\Composer\Helper;

class ComposerCleaner {

    private $_components;
    private $_logger;

    public function __construct($components, \Magento\Composer\Log\Log $logger){
        $this->_components = $components;
        $this->_logger = $logger;
    }

    public function clean(){
        foreach($this->_components as $component){
            $fileLocation = $component->getLocation() . "/composer.json";
            if(file_exists($fileLocation)){
                unlink($fileLocation);
                $this->_logger->debug("Cleared composer.json on %-40s", $component->getName());
            } else {
                $this->_logger->debug("Skipped. composer.json doesn't exist for %s", $component->getName());
            }
        }
        return sizeof($this->_components);
    }

}