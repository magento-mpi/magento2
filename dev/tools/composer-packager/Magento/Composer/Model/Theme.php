<?php

namespace Magento\Composer\Model;

class Theme implements \Magento\Composer\Model{

    public $_name;
    public $_version;
    public $_location;
    public $_type;
    private $_dependencies =null;

    public function __construct($name, $version = null,  $location=null){
        $this->setName($name);
        $this->setVersion($version);
        $this->setLocation($location);
        $this->_dependencies =  new \ArrayObject(array());
    }

    public function setType($type){
        $this->_type = $type;
        return $this;
    }

    public function getType(){
        return $this->_type;
    }

    public function getName(){
        return $this->_name;
    }

    public function getVersion(){
        return $this->_version;
    }

    public function getLocation(){
        return $this->_location;
    }

    public function setName($name){
        //For Naming Conflicts
        $this->_name = $name;
        return $this;
    }

    public function setVersion($version){
        //This is the snippet of code that brings the version depth to 4 level only.
        //Something that composer cries about, if it is higher than 4.
        $parts  = explode('.', $version);
        $output = implode('.', array_slice($parts, 0, 4));
        $this->_version = $output;
        return $this;
    }


    public function setLocation($location){
        $this->_location = $location;
    }

    public function addDependencies($dependencies){
        $this->_dependencies->append($dependencies);
        return $this;
    }

    public function getDependencies(){
        return $this->_dependencies;
    }
}