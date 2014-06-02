<?php

namespace Magento\Composer\Extractor;

class LanguagePackExtractor extends  AbstractExtractor{

    public function getSubPath(){
        return '/app/i18n/Magento/';
    }

    public function getType(){
        return "magento2-language";
    }

    public function getParser($filename){
        return new \Magento\Composer\Parser\LanguagePackXmlParser($filename);
    }

    public function createComponent($name){
        return new \Magento\Composer\Model\LanguagePack($name);
    }

}