<?php

namespace Magento\Composer\Extractor;

class AdminThemeExtractor extends  AbstractExtractor{

    public function getSubPath(){
        return '/app/design/adminhtml/Magento/';
    }

    public function getType(){
        return "magento2-theme-adminhtml";
    }

    public function getParser($filename){
        return new \Magento\Composer\Parser\ThemeXmlParser($filename);
    }

    public function createComponent($name){
       return new \Magento\Composer\Model\Theme($name);
    }

}