<?php

namespace Magento\Composer\Extractor;

class ModuleExtractor extends  AbstractExtractor{

    public function getSubPath(){
        return '/app/code/Magento/';
    }

    public function getType(){
        return "magento2-module";
    }

    public function getParser($filename){
        return new \Magento\Composer\Parser\ModuleXmlParser($filename);
    }

    public function createComponent($name){
        return new \Magento\Composer\Model\Module($name);
    }

}