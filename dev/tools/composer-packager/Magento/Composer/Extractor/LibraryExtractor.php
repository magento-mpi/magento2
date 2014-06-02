<?php

namespace Magento\Composer\Extractor;

class LibraryExtractor extends  FrameworkExtractor{

    public function getSubPath(){
        return '/lib/';
    }

    public function extract($collection = array(), &$count = 0){
        $this->_counter = &$count;
        $this->_counter = 0;
        $this->addToCollection($collection);
        $parser = $this->getParser($this->getPath());
        $definition = $parser->getMappings();
        $this->create($definition);
        return $this->_collection;
    }


    public function getType(){
        return "magento2-library";
    }

    public function createComponent($name){
        return new \Magento\Composer\Model\Library($name);
    }

}