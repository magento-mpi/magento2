<?php

namespace Magento\Composer\Extractor;

class FrameworkExtractor extends  AbstractExtractor{

    public function getSubPath(){
        return "/lib/Magento/";
    }

    public function getType(){
        return "magento2-framework";
    }

    public function getParser($filename){
        return new \Magento\Composer\Parser\LibraryXmlParser($filename);
    }

    public function createComponent($name){
        return new \Magento\Composer\Model\Library($name);
    }

}