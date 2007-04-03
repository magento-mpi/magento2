<?php

/**
 * Resources and connections registry and factory
 *
 */
class Mage_Core_Resource
{
    /**
     * Instances of classes for connection types
     *
     * @var array
     */
    protected $_connectionTypes = array();
    
    /**
     * Instances of actual connections
     *
     * @var array
     */
    protected $_connections = array();
    
    /**
     * Miscellanious information about resources
     *
     * @var array
     */
    protected $_resources = array();
    
    /**
     * Registry of resource entities
     *
     * @var array
     */
    protected $_entities = array();

    /**
     * Get connection type instance
     * 
     * Creates new if doesn't exist
     *
     * @param string $type
     * @return Mage_Core_Resource_Type_Abstract
     */
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
    
    /**
     * Creates a connection to resource whenever needed
     *
     * @param string $name
     * @return mixed
     */
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
    
    /**
     * Get resource entity
     *
     * @param string $resource
     * @param string $entity
     * @return Varien_Simplexml_Config
     */
    function getEntity($resource, $entity)
    {
        if (!isset($this->_entities[$resource][$entity])) {
            $entities = Mage::getConfig()->getResourceConfig($resource)->entities;
            $this->_entities[$resource][$entity] = $entities->$entity;
        }
        return $this->_entities[$resource][$entity];
    }
}