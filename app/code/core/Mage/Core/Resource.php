<?php

class Mage_Core_Resource
{
    protected $_connectionTypes = array();
    protected $_connections = array();
    protected $_resources = array();
    protected $_entities = array();

    function getConnectionTypeObj($type)
    {
        if (!isset($this->_connectionTypes[$type])) {
            $config = Mage::getConfig()->getResourceType($type);
            $typeClass = (string)$config->class;
            $this->_connectionTypes[$type] = new $typeClass();
            #$this->_connectionTypes[$type]->setName($type);
        }
        return $this->_connectionTypes[$type];
    }
    
    function getConnection($name)
    {
        if (!isset($this->_connections[$name])) {
            $conn = Mage::getConfig()->getResourceConfig($name)->connection;
            $use = $conn['use'];
            if (!empty($use)) {
                $this->_connections[$name] = $this->getConnection((string)$use);
            } else {
                $typeObj = $this->getConnectionTypeObj((string)$conn->type);
                $this->_connections[$name] = $typeObj->getConnection($conn);
            }
            #$this->_resources[$name]['type'] = $typeObj->getName();
        }
        return $this->_connections[$name];
    }
    
    function getEntity($resource, $entity)
    {
        if (!isset($this->_entities[$resource][$entity])) {
            $entities = Mage::getConfig()->getResourceConfig($resource)->entities;
            $this->_entities[$resource][$entity] = $entities->$entity;
        }
        return $this->_entities[$resource][$entity];
    }
}