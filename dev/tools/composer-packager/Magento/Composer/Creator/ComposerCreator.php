<?php

namespace Magento\Composer\Creator;

class ComposerCreator implements \Magento\Composer\Creator{

    private $_components;
    private $_logger;

    public function __construct($components, \Magento\Composer\Log\Log $logger){
        $this->_components = $components;
        $this->_logger = $logger;
    }

    public function create(){
        $counter = 0;
        foreach($this->_components as $component){
            $command = "cd ".$component->getLocation() ." && php ".__DIR__."/../../../composer.phar init  --name \"". $component->getName(). "\" --description=\"This is the description\" --author=\"Jay Patel <jaypatel512@gmail.com>\" --stability=\"dev\" -n";
            //Command to include package installer.
            $dependencies = $component->getDependencies();
            foreach($dependencies as $dependency){
                $command .= " --require=\"" . $dependency->getName().":".$dependency->getVersion()."\" ";
            }
            $command .= " --require=\"Magento/Package-installer:*\"";
            //        echo $command, "\n";
            $output = array();
            exec($command, $output);
            if(sizeof($output) > 0 ){
                //Failed
                print_r($output);
            } else {
                //Success
                $this->addVersionandTypeInfo($component);
                $counter++;
                $this->_logger->debug("Created composer.json for %-40s [%9s]", $component->getName(), $component->getVersion());
            }

        }
        return $counter;
    }

    public function addVersionandTypeInfo(\Magento\Composer\Model $component){
        if($component->getVersion() != null && $component->getVersion() != ""){
            $json = file_get_contents($component->getLocation()."/composer.json");
            $composer = json_decode($json, true);
            if(!array_key_exists("type", $composer)){
                $composer["type"] = $component->getType();
            }
            if(!array_key_exists("version", $composer)){
                $composer["version"] = $component->getVersion();
            }
            file_put_contents($component->getLocation()."/composer.json", json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}