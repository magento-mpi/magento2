<?php
#include_once 'Ecom/Core/Resource/Entity/Abstract.php';

abstract class Ecom_Core_Resource_Abstract
{
    protected $_connection = null;
    protected $_config = array();
    protected $_entities = array();
    protected $_defaultEntityClass = 'Ecom_Core_Resource_Entity_Abstract';
    
    public function __construct()
    {
        
    }
      
    public function getConnection()
    {
        return $this->_connection;
    }
    
    public function setConnection($connection)
    {
        $this->_connection = $connection;
    }
    
    public function getConfig($key='')
    {
        if (''===$key) {
            return $this->_config;
        } else {
            return isset($this->_config[$key]) ? $this->_config[$key] : false;
        }
    }
    
    public function setConfig($key, $value=null)
    {
        if (null===$value) {
            $this->_config = $key;
        } else {
            $this->_config[$key] = $value;
        }
    }
    
    public function addEntity($name, Ecom_Core_Resource_Entity_Abstract $entity)
    {
        $entity->setData('name', $name);
        $this->_entities[$name] = $entity;
    }
    
    public function getEntity($name)
    {
        if (!isset($this->_entities[$name])) {
            Ecom::exception('Invalid entity requested from resource '.$this->getConfig('name').': '.$name);
        }
        return $this->_entities[$name];
    }

    public function loadEntitiesArray($config)
    {
        $className = $this->_defaultEntityClass;
        #Ecom::loadClass($className);
        
        foreach ($config as $name=>$config) {
            $entity = new $className($config->asArray());
            $this->addEntity($name, $entity);
        }        
    }
}