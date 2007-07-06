<?php

/**
 * Abstract resource model class
 *
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Mage
 * @subpackage  Core
 * @author      Moshe Gurvich <moshe@varien.com>
 */
abstract class Mage_Core_Model_Resource_Abstract
{
    /**
     * Cached resources singleton
     *
     * @var Mage_Core_Model_Resource
     */
    protected $_resources;
    
    /**
     * Prefix for resources that will be used in this resource model
     *
     * @var string
     */
    protected $_resourcePrefix;
    
    /**
     * Connections cache for this resource model
     *
     * @var array
     */
    protected $_connections = array();
    
    /**
     * Resource model name that contains entities (names of tables)
     *
     * @var string
     */
    protected $_resourceModel;
    
    /**
     * Tables used in this resource model
     *
     * @var array
     */
    protected $_tables = array();
    
    /**
     * Main table name
     *
     * @var string
     */
    protected $_mainTable;
    
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName;
    
    /**
     * Initialize connections and tables for this resource model
     * 
     * If one or both arguments are string, will be used as prefix
     * If $tables is null and $connections is string, $tables will be the same
     *
     * @param string|array $connections
     * @param string|array|null $tables
     * @return Mage_Core_Model_Resource_Abstract
     */
    protected function _setResource($connections, $tables=null)
    {
        $this->_resources = Mage::getSingleton('core/resource');
        
        if (is_array($connections)) {
            foreach ($connections as $k=>$v) {
                $this->_connections[$k] = $this->_resources->getConnection($v);
            }
        } elseif (is_string($connections)) {
            $this->_resourcePrefix = $connections;
        }
        
        if (is_null($tables) && is_string($connections)) {
            $this->_resourceModel = $this->_resourcePrefix;
        } elseif (is_array($tables)) {
            foreach ($tables as $k=>$v) {
                $this->_tables[$k] = $this->_resources->getTableName($v);
            }
        } elseif (is_string($tables)) {
            $this->_resourceModel = $tables;
        }
        return $this;
    }
    
    /**
     * Set main entity table name and primary key field name
     * 
     * If field name is ommited {table_name}_id will be used
     *
     * @param string $mainTable
     * @param string|null $idFieldName
     * @return Mage_Core_Model_Resource_Abstract
     */
    protected function _setMainTable($mainTable, $idFieldName=null)
    {
        $this->_mainTable = $mainTable;
        if (is_null($idFieldName)) {
            $idFieldName = $mainTable.'_id';
        }
        $this->_idFieldName = $idFieldName;
        return $this;
    }
    
    /**
     * Get primary key field name 
     *
     * @return string
     */
    public function getIdField()
    {
        if (empty($this->_idFieldName)) {
            throw Mage::exception('Mage_Core', 'Empty field id name');
        }
        return $this->_idFieldName;
    }
    
    /**
     * Get main table name
     *
     * @return string
     */
    public function getMainTable()
    {
        if (empty($this->_idFieldName)) {
            throw Mage::exception('Mage_Core', 'Empty main table name');
        }
        return $this->getTable($this->_mainTable);
    }
    
    /**
     * Get table name for the entity
     *
     * @param string $entityName
     */
    public function getTable($entityName)
    {
        if (isset($this->_tables[$entityName])) {
            return $this->_tables[$entityName];
        }
        if (strpos($entityName, '/')) {
            $this->_tables[$entityName] = $this->_resources->getTableName($entityName);
        } elseif (!empty($this->_resourceModel)) {
            $this->_tables[$entityName] = $this->_resources->getTableName($this->_resourceModel.'/'.$entityName);
        } else {
            $this->_tables[$entityName] = $entityName;
        }
        return $this->_tables[$entityName];
    }
    
    /**
     * Get connection by name or type
     *
     * @param string $connectionName
     * @return string
     */
    public function getConnection($connectionName)
    {
        if (isset($this->_connections[$connectionName])) {
            return $this->_connections[$connectionName];
        }
        if (!empty($this->_resourcePrefix)) {
            $this->_connections[$connectionName] = $this->_resources->getConnection($this->_resourcePrefix.'_'.$connectionName);
        } else {
            $this->_connections[$connectionName] = $this->_resources->getConnection($connectionName);
        }
        return $this->_connections[$connectionName];
    }
    
    /**
     * Load an object
     *
     * @param Varien_Object $object
     * @param integer $id
     * @return boolean
     */
    public function load(Varien_Object $object, $id)
    {
        $read = $this->getConnection('read');
        
        $select = $read->select()->from($this->getMainTable())->where($this->getIdField().'=?', $id);
        $data = $read->fetchRow($select);
        
        if (!$data) {
            return false;
        }
        
        $object->setData($data);

        $this->_afterLoad($object);
        
        return true;
    }
    
    /**
     * Save an object
     *
     * @param Varien_Object $object
     */
    public function save(Varien_Object $object)
    {
        $write = $this->getConnection('write');
        $table = $this->getMainTable();
        
        $write->beginTransaction();
        
        try {
            $this->_beforeSave($object);
            
            if ($object->getId()) {
                $condition = $this->_write->quoteInto($this->getIdField().'=?', $object->getId());
                $write->update($table, $object->getData(), $condition);
            } else {
                $write->insert($table, $object->getData());
                $object->setId($write->lastInsertId($table));
            }
            
            $write->commit();
            
            $this->_afterSave($object);
            
        } catch (Exception $e) {
            
            $write->rollBack();
            Mage::throwException('Exception while saving the object');
            
        }
        
        return $this;
    }
    
    /**
     * Delete the object
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function delete(Varien_Object $object)
    {
        $write = $this->getConnection('write');
        $table = $this->getMainTable();
        
        $write->beginTransaction();
        try {
            $this->_beforeDelete($object);
            
            $write->delete($table, $write->quoteInto($this->getIdField().'=?', $object->getId()));
            $write->commit();
            
            $this->_afterDelete($object);
            
        } catch(Exception $e) {
            $write->rollBack();
            Mage::throwException('Exception while deleting the object');
        }
        return $this;
    }
    
    /**
     * Perform actions after object load
     *
     * @param Varien_Object $object
     */
    protected function _afterLoad(Varien_Object $object)
    {
        
    }
    
    /**
     * Perform actions before object save
     *
     * @param Varien_Object $object
     */
    protected function _beforeSave(Varien_Object $object)
    {
        
    }    
    
    /**
     * Perform actions after object save
     *
     * @param Varien_Object $object
     */
    protected function _afterSave(Varien_Object $object)
    {
        
    }
    
    /**
     * Perform actions before object delete
     *
     * @param Varien_Object $object
     */
    protected function _beforeDelete(Varien_Object $object)
    {
        
    }  
      
    /**
     * Perform actions after object delete
     *
     * @param Varien_Object $object
     */
    protected function _afterDelete(Varien_Object $object)
    {
        
    }
}