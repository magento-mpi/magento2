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
     * Registry of resource entities
     *
     * @var array
     */
    protected $_entities = array();
 
    /**
     * Creates a connection to resource whenever needed
     *
     * @param string $name
     * @return mixed
     */
    public function getConnection($name)
    {
        if (!isset($this->_connections[$name])) {
            $conn = Mage::getConfig()->getResourceConnectionConfig($name);
            $typeObj = $this->getConnectionTypeObj((string)$conn->type);
            $this->_connections[$name] = $typeObj->getConnection($conn);
        }
        return $this->_connections[$name];
    }
    
    /**
     * Get connection type instance
     *
     * Creates new if doesn't exist
     *
     * @param string $type
     * @return Mage_Core_Resource_Type_Abstract
     */
    public function getConnectionTypeObj($type)
    {
        if (!isset($this->_connectionTypes[$type])) {
            $config = Mage::getConfig()->getResourceTypeConfig($type);
            $typeClass = $config->getClassName();
            $this->_connectionTypes[$type] = new $typeClass();
        }
        return $this->_connectionTypes[$type];
    }

    /**
     * Get resource entity
     *
     * @param string $resource
     * @param string $entity
     * @return Varien_Simplexml_Config
     */
    public function getEntity($model, $entity)
    {
        if (!isset($this->_entities[$model][$entity])) {
            $entities = Mage::getConfig()->getGlobalCollection('models', $model)->entities;
            $this->_entities[$model][$entity] = $entities->$entity;
        }
        return $this->_entities[$model][$entity];
    }
    
    public function getTableName($model, $entity)
    {
        return (string)$this->getEntity($model, $entity)->table;
    }
}