<?php

/**
 * Entity/Attribute/Model - entity abstract
 *
 * @package    Mage
 * @subpackage Mage_Eav
 * @author     Moshe Gurvich moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Eav_Model_Entity_Abstract implements Mage_Eav_Model_Entity_Interface
{
    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;
    
    /**
     * Write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    /**
     * Entity type configuration
     *
     * @var Mage_Core_Model_Config_Element
     */
    protected $_config;
    
    /**
     * Object to operate with (load, save, optionally delete)
     *
     * @var Varien_Object
     */
    protected $_object;
    
    /**
     * Current store id to retrieve entity for
     *
     * @var integer
     */
    protected $_storeId;
    
    /**
     * Store Ids that share data for this entity
     *
     * @var array
     */
    protected $_sharedStoreIds=array();
    
    /**
     * Attributes array by attribute id
     *
     * @var array
     */
    protected $_attributesById = array();
    
    /**
     * Attributes array by attribute name
     *
     * @var unknown_type
     */
    protected $_attributesByName = array();
    
    /**
     * 2-dimentional array by table name and attribute name
     *
     * @var array
     */
    protected $_attributesByTable = array();
    
    /**
     * Attributes that are static fields in entity table
     *
     * @var array
     */
    protected $_staticAttributes = array();
    
    protected $_entityTable;
    
    protected $_entityIdField;
    
    protected $_valueEntityIdField;
    
    protected $_valueTablePrefix;
    
    /**
     * Success/error messages
     *
     * @var Mage_Core_Model_Message_Collection
     */
    protected $_messages;
    
    /**
     * Set connections for entity operations
     *
     * @param Zend_Db_Adapter_Abstract $read
     * @param Zend_Db_Adapter_Abstract $write
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function setConnection(Zend_Db_Adapter_Abstract $read, Zend_Db_Adapter_Abstract $write=null)
    {
        $this->_read = $read;
        $this->_write = $write ? $write : $read;
        return $this;
    }
    
    public function getReadConnection()
    {
        return $this->_read;
    }
    
    public function getWriteConnection()
    {
        return $this->_write;
    }

    /**
     * Set configuration for the entity
     * 
     * Accepts config node or name of entity type
     *
     * @param string|Mage_Core_Model_Config_Element $type
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function setType($type)
    {
        if (is_string($type)) {
            $config = Mage::getConfig()->getNode("global/entities/$type");
        } elseif ($type instanceof Mage_Core_Model_Config_Element) {
            $config = $type;
        } else {
            throw Mage::exception('Mage_Eav', 'Unknown parameter');
        }
        
        if (!$config) {
            throw Mage::exception('Mage_Eav', 'Invalid entity type '.$type);
        }

        $this->_config = $config;
        
        $this->_afterSetConfig();
        
        return $this;
    }
    
    /**
     * Retrieve current entity config
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfig()
    {
        if (empty($this->_config)) {
            throw Mage::exception('Mage_Eav', 'Entity is not initialized');
        }
        return $this->_config;
    }
    
    /**
     * Set object to work with
     *
     * @deprecated to be able to use entity as singleton
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function setObject(Varien_Object $object)
    {
        $this->_object = $object;
        return $this;
    }
    
    /**
     * Get object current entity's working with
     *
     * @deprecated to be able to use entity as singleton
     * @return Varien_Object
     */
    public function getObject()
    {
        if (empty($this->_object)) {
            throw Mage::exception('Mage_Eav', "Entity's object is not initialized");
        }
        return $this->_object;
    }

    /**
     * Get entity type name
     *
     * @return string
     */
    public function getType()
    {
        return $this->getConfig()->getName();
    }
    
    /**
     * Get entity type id
     *
     * @return integer
     */
    public function getTypeId()
    {
        return (int)$this->getConfig()->id;
    }
    
    public function getMessages()
    {
        if (empty($this->_messages)) {
            $this->_messages = Mage::getModel('core/message_collection');
        }
        return $this->_messages;
    }
    
    /**
     * Retrieve whether to support data sharing between stores for this entity
     * 
     * Basically that means 2 things:
     * - entity table has store_id field which describes the originating store
     * - store_id is being filtered by all participating stores in share
     *
     * @return boolean
     */
    public function getUseDataSharing()
    {
        return $this->getConfig()->is('use_data_sharing');
    }
    
    /**
     * Set store for which entity will be retrieved
     *
     * @param integer|string|Mage_Core_Model_Store $store
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function setStore($storeId=null)
    {
        $current = Mage::getSingleton('core/store');
        
        if (empty($storeId)) {
            
            $store = $current;
            $this->_storeId = $store->getId();
            
        } elseif (is_numeric($storeId)) {
            
            $this->_storeId = $storeId;
            if ($storeId===$current->getId()) {
                $store = $current;
            } else {
                $store = Mage::getModel('core/store')->findById($store);
            }
            
        } elseif (is_string($storeId)) {
            
            if ($storeId===$current->getCode()) {
                $store = $current;
            } else {
                $store = Mage::getModel('core/store')->setCode($storeId);
            }
            $this->_storeId = $store->getId();
            
        } elseif ($storeId instanceof Mage_Core_Model_Store) {
            
            $store = $storeId;
            $this->_storeId = $storeId->getId();
            
        } else {
            throw Mage::exception('Mage_Eav', 'Invalid store id supplied');
        }
        
        $this->_sharedStoreIds = $store->getDatashareStores($this->getType());
        if (empty($this->_sharedStoreIds)) {
            $this->_sharedStoreIds = array($this->_storeId);
        }
        
        return $this;
    }
    
    /**
     * Get current store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if (empty($this->_storeId)) {
            $this->setStore();
        }
        return $this->_storeId;
    }
    
    public function getSharedStoreIds()
    {
        if (empty($this->_sharedStoreIds)) {
            $this->setStore();
        }
        return $this->_sharedStoreIds;
    }
    
    /**
     * Unset attributes
     * 
     * If NULL or not supplied removes configuration of all attributes
     * If string - removes only one, if array - all specified
     *
     * @param array|string|null $attributes
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function unsetAttributes($attributes=null)
    {
        if (empty($attributes)) {
            $this->_attributesByName = array();
            $this->_attributesById = array();
            $this->_attributesByTable = array();
            return $this;
        }
        
        if (is_string($attributes)) {
            $attributes = array($attributes);
        }
        
        if (!is_array($attributes)) {
            throw Mage::exception('Mage_Eav', 'Unknown parameter');
        }
        
        foreach ($attributes as $attrName) {
            if (!isset($this->_attributesByName[$attrName])) {
                continue;
            }
            
            $attr = $this->getAttribute($attrName);
            unset($this->_attributesById[$attr->getId()]);
            unset($this->_attributesByTable[$attr->getBackend()->getTable()][$attrName]);
            unset($this->_attributesByName[$attrName]);
        }
        
        return $this;
    }
    
    /**
     * Retrieve attribute instance by name, id or config node
     * 
     * This will add the attribute configuration to entity's attributes cache
     * 
     * If attribute is not found false is returned
     *
     * @param string|integer|Mage_Core_Model_Config_Element $attribute
     * @return boolean|Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($attribute)
    {
        if (is_numeric($attribute)) {

            $attributeId = $attribute;
            
            if (isset($this->_attributesById[$attributeId])) {
                return $this->_attributesById[$attributeId];
            }
            
            $attributes = $this->getConfig()->attributes;
            $attributeConfig = false;
            foreach ($attributes->children() as $attrName=>$attrConfig) {
                if ((int)$attrConfig->id===$attributeId) {
                    $attributeName = $attrName;
                    $attributeConfig = $attrConfig;
                    break;
                }
            }
        } elseif (is_string($attribute)) {
            
            $attributeName = $attribute;

            if (isset($this->_attributesByName[$attributeName])) {
                return $this->_attributesByName[$attributeName];
            }
            $attributeConfig = $this->getConfig()->attributes->$attributeName;
     
        } elseif ($attribute instanceof Mage_Core_Model_Config_Element) {
     
            $attributeConfig = $attribute;
            $attributeName = $attributeConfig->getName();
            if (isset($this->_attributesByName[$attributeName])) {
                return $this->_attributesByName[$attributeName];
            }
        }

        if (empty($attributeConfig) && !($attributeConfig instanceof Mage_Eav_Model_Entity_Attribute_Abstract)) {
            return false;
        }
        
        if (empty($attributeId)) {
            $attributeId = (int)$attributeConfig->id;
        }
        
        if (empty($attributeConfig->model)) {
            $attributeConfig->addChild('model', $this->_getDefaultAttributeModel());
        }

        $attributeInstance = Mage::getModel((string)$attributeConfig->model)
            ->setConfig($attributeConfig)
            ->setName($attributeName)
            ->setEntity($this);

        $this->_attributesByName[$attributeName] = $attributeInstance;
        
        if ($attributeInstance->getBackend()->isStatic()) {
            $this->_staticAttributes[$attributeName] = $attributeInstance;
        } else {
            $this->_attributesById[$attributeId] = $attributeInstance;
            
            $attributeTable = $attributeInstance->getBackend()->getTable();
            $this->_attributesByTable[$attributeTable][$attributeName] = $attributeInstance;
        }

        return $attributeInstance;
    }
    
    /**
     * Retrieve configuration for all attributes
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function loadAllAttributes()
    {
        $attributes = $this->getConfig()->attributes;
        if (empty($attributes)) {
            throw Mage::exception('Mage_Eav', 'No attributes specified for entity');
        }
        
        foreach ($attributes->children() as $attrName=>$attribute) {
            if (isset($this->_attributesByName[$attrName])) {
                continue;
            }
            $this->getAttribute($attribute);
        }
        return $this;
    }
    
    /**
     * Walk through the attributes and run method with optional arguments
     *
     * Returns array with results for each attribute
     * 
     * if $method is in format "part/method" will run method on specified part
     * for example: $this->walkAttributes('backend/validate');
     * 
     * @param string $method
     * @param array $args
     * @param array $part attribute, backend, frontend, source
     * @return array
     */
    public function walkAttributes($partMethod, array $args=array())
    {
        $methodArr = explode('/', $partMethod);
        switch (sizeof($methodArr)) {
            case 1:
                $part = 'attribute';
                $method = $methodArr[0];
                break;
                
            case 2:
                $part = $methodArr[0];
                $method = $methodArr[1];
                break;
        }
        $results = array();
        foreach ($this->getAttributesByName() as $attrName=>$attribute) {
            switch ($part) {
                case 'attribute':
                    $instance = $attribute;
                    break;
                    
                case 'backend':
                    $instance = $attribute->getBackend();
                    break;
                    
                case 'frontend':
                    $instance = $attribute->getFrontend();
                    break;
                    
                case 'source':
                    $instance = $attribute->getSource();
                    break;
            }
            $results[$attrName] = call_user_func_array(array($instance, $method), $args);
        }
        return $results;
    }

    /**
     * Get attributes by name array
     *
     * @return array
     */
    public function getAttributesByName()
    {
        return $this->_attributesByName;
    }
    
    /**
     * Get attributes by id array
     *
     * @return array
     */
    public function getAttributesById()
    {
        return $this->_attributesById;
    }
    
    /**
     * Get attributes by table and name array
     *
     * @return array
     */
    public function getAttributesByTable()
    {
        return $this->_attributesByTable;
    }

    /**
     * Get entity table name
     *
     * @return string
     */
    public function getEntityTable()
    {
        if (empty($this->_entityTable)) {
            $table = (string)$this->getConfig()->descend('backend/entity_table');
            if (empty($table)) {
                $table = Mage_Eav_Model_Entity::DEFAULT_ENTITY_TABLE;
            }
            $this->_entityTable = Mage::getSingleton('core/resource')->getTableName($table);
        }
        return $this->_entityTable;
    }
        
    /**
     * Get entity id field name in entity table
     *
     * @return string
     */
    public function getEntityIdField()
    {
        if (empty($this->_entityIdField)) {
            $this->_entityIdField = (string)$this->getConfig()->descend('backend/entity_id_field');
            if (empty($this->_entityIdField)) {
                $this->_entityIdField = Mage_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD;
            }
        }
        return $this->_entityIdField;
    }
    
    /**
     * Get default entity id field name in attribute values tables
     *
     * @return string
     */
    public function getValueEntityIdField()
    {
        if (empty($this->_valueEntityIdField)) {
            $this->_valueEntityIdField = (string)$this->getConfig()->descend('backend/value_entity_id_field');
            if (empty($this->_valueEntityIdField)) {
                $this->_valueEntityIdField = Mage_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD;
            }
        }
        return $this->_valueEntityIdField;
    }
    
    /**
     * Get prefix for value tables
     *
     * @return string
     */
    public function getValueTablePrefix()
    {
        if (empty($this->_valueTablePrefix)) {
            $prefix = (string)$this->getConfig()->descend('backend/value_table_prefix');
            if (!empty($prefix)) {
                $this->_valueTablePrefix = Mage::getSingleton('core/resource')->getTableName($prefix);
            } else {
                $this->_valueTablePrefix = $this->getEntityTable();
            }
        }
        return $this->_valueTablePrefix;
    }
    
    /**
     * Check whether the attribute is a real field in entity table
     *
     * @see Mage_Eav_Model_Entity_Abstract::getAttribute for $attribute format
     * @param integer|string|Mage_Core_Model_Config_Element $attribute
     * @return unknown
     */
    public function isAttributeStatic($attribute)
    {
        $attrInstance = $this->getAttribute($attribute);
        return $attrInstance && $attrInstance->getBackend()->isStatic();
    }
    
    /**
     * Validate all object's attributes against configuration
     * 
     * @param Varien_Object $object
     * @return Varien_Object
     */
    public function validate($object)
    {
        $this->loadAllAttributes();
        $this->walkAttributes('backend/validate', array($object));
        
        return $this;
    }

    /**
     * Load entity's attributes into the object
     *
     * @param Varien_Object $object
     * @param integer $entityId
     * @param array $attributes
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function load($object, $entityId, array $attributes=array())
    {
        if (!$this->_read) {
            throw Mage::exception('Mage_Eav', 'No connection available');
        }
        
        #$object = $this->getObject();
        
        $select = $this->_read->select()->from($this->getEntityTable());
        $select->where($this->getEntityIdField()."=?", $entityId);
        
        if ($this->getUseDataSharing()) {
            $select->where("store_id in (?)", $this->getSharedStoreIds());
        }

        $row = $this->_read->fetchRow($select);
        $object->setData($row);
    
        if ($this->getUseDataSharing()) {
            $storeId = $row['store_id'];
        } else {
            $storeId = $this->getStoreId();
        }
        
        if (empty($attributes)) {
            $this->loadAllAttributes();
        } else {
            foreach ($attributes as $attrName) {
                $this->getAttribute($attrName);
            }
        }
        foreach ($this->getAttributesByTable() as $table=>$attributes) {
            $entityIdField = current($attributes)->getBackend()->getEntityIdField();
            #$sql = "select attribute_id, value from $table where $entityIdField=".(int)$entityId." and store_id=".$storeId;
            $select = $this->_read->select()->from($table)->where("$entityIdField=?", $entityId)->where("store_id=?", $storeId);
            $values = $this->_read->fetchAll($select);
            if (empty($values)) {
                continue;
            }
            
            foreach ($values as $v) {
                $attributeName = $this->getAttribute($v['attribute_id'])->getName();
                $object->setData($attributeName, $v['value']);
                $this->getAttribute($v['attribute_id'])->getBackend()->setValueId($v['value_id']);
            }
        }
        
        $this->_afterLoad($object);
        
        return $this;
    }

    /**
     * Save entity's attributes into the object's resource
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function save(Varien_Object $object)
    {
        if (!$this->_write) {
            throw Mage::exception('Mage_Eav', 'No connection available');
        }
        
        if (!$object->getEntityTypeId()) {
            $object->setEntityTypeId($this->getTypeId());
        }
        
        if ($this->getUseDataSharing() && !$object->getStoreId()) {
            $object->setStoreId($this->getStoreId());
        }
        
        $this->_write->beginTransaction();
        
        $this->_beforeSave($object);

        try {
            $this->_processSaveData($this->_collectSaveData($object));
            $this->_write->commit();
        } catch (Exception $e) {
            $this->_write->rollback();
            throw $e;
        }

        $this->_afterSave($object);

        
        return $this;
    }
    
    
    public function saveAttribute(Varien_Object $object, $attributeName)
    {
        $attribute = $this->getAttribute($attributeName);
        $backend = $attribute->getBackend();
        $table = $backend->getTable();
        $entity = $attribute->getEntity();
        $entityIdField = $entity->getEntityIdField();
        $row = array(
            'entity_type_id' => $entity->getTypeId(),
            'attribute_id' => $attribute->getId(),
            'store_id' => $object->getStoreId(),
            $entityIdField=> $object->getData($entityIdField),
        );
        $newValue = $object->getData($attributeName);
        $whereArr = array();
        foreach ($row as $f=>$v) {
            $whereArr[] = $this->_read->quoteInto("$f=?", $v);
        }
        $where = '('.join(') AND (', $whereArr).')';
        
        $this->_write->beginTransaction();
        
        try {
            $select = $this->_read->select()->from($table, 'value')->where($where);
            $origValue = $this->_read->fetchOne($select);
            
            if (empty($origValue) && !empty($newValue)) {
                
                $row['value'] = $newValue;
                $this->_write->insert($table, $row);
                $backend->setValueId($this->_write->lastInsertId());
                
            } elseif (!empty($origValue) && !empty($newValue)) {
                
                $this->_write->update($table, array('value'=>$newValue), $where);
                
            } elseif (!empty($origValue) && empty($newValue)) {
                
                $this->_write->delete($table, $where);
                
            }
            
            $this->_write->commit();
        } catch (Exception $e) {
            $this->_write->rollback();
            throw $e;
        }
            
        return $this;
    }

    /**
     * Delete entity using current object's data
     * 
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function delete($object)
    {
        if (!$this->_write) {
            throw Mage::exception('Mage_Eav', 'No connection available');
        }
        #$object = $this->getObject();
        
        if (is_numeric($object)) {
            $id = (int)$object;
        } elseif ($object instanceof Varien_Object) {
            $id = (int)$object->getData($this->getEntityIdField());
        }
        
        $this->_write->beginTransaction();

        $this->_beforeDelete($object);
        
        try {
            $this->_write->delete($this->getEntityTable(), $this->getEntityIdField()."=".$id);
            $this->loadAllAttributes();
            foreach ($this->getAttributesByTable() as $table=>$attributes) {
                $this->_write->delete($table, $this->getEntityIdField()."=".$id);
            }
        } catch (Exception $e) {
            $this->_write->rollback();
            throw $e;
        }

        $this->_afterDelete($object);
        
        $this->_write->commit();
        
        return $this;
    }
    
    protected function _collectSaveData($newObject)
    {
        #$newObject = $this->getObject();
        $newData = $newObject->getData();
        
        $entityId = $newObject->getData($this->getEntityIdField());
        if (!empty($entityId)) {
            // get current data in db for this entity
            $origObject = clone $newObject;
            $origObject->setData(array());
            //$this->load($origObject, $entityId, array_keys($this->_attributesByName));
            $this->load($origObject, $entityId);
            $origData = $origObject->getData();
            // drop attributes that are unknown in new data
            // not needed after introduction of partial entity loading
            foreach ($origData as $k=>$v) {
                if (!isset($newData[$k])) {
                    unset($origData[$k]);
                    continue;
                }
            }
            
        }

        foreach ($newData as $k=>$v) {
            if (is_numeric($k)) {
                continue;
                throw Mage::exception('Mage_Eav', 'Invalid data object key');
            }
            
            $attribute = $this->getAttribute($k);
            if (empty($attribute)) {
                continue;
            }
            
            $attrId = $attribute->getId();
            // if attribute is static add to entity row and continue
            if ($this->isAttributeStatic($k)) {
                $entityRow[$k] = $v;
                unset($newData[$k]);
                if (isset($origData)) {
                    unset($origData[$k]);
                }
                continue;
            }
            
            if (isset($origData[$k])) {
                if (empty($v)) {
                    $delete[$attribute->getBackend()->getTable()][] = $attribute->getBackend()->getValueId();
                } elseif ($v!==$origData[$k]) {
                    $update[$attrId] = array(
                        'value_id'=>$attribute->getBackend()->getValueId(),
                        'value'=>$v
                    );
                }
            } 
            elseif (!empty($v)) {
                $insert[$attrId] = $v;
            }
        }
        
        $result = compact('newObject', 'entityRow', 'insert', 'update', 'delete');

        return $result;
    }
    
    protected function _processSaveData($saveData)
    {
        extract($saveData);

        $entityIdField = $this->getEntityIdField();
        $entityId = $newObject->getData($entityIdField);

        if (empty($entityId)) {
            // insert entity table row
            $this->_write->insert($this->getEntityTable(), $entityRow);
            $entityId = $this->_write->lastInsertId();
            $newObject->setData($entityIdField, $entityId);
        } else {    
            // update entity table row
            $condition = $this->_write->quoteInto("$entityIdField=?", $entityId);
            $this->_write->update($this->getEntityTable(), $entityRow, $condition);
        }

        // insert attribute values
        if (!empty($insert)) {
            foreach ($insert as $attrId=>$value) {
                $attribute = $this->getAttribute($attrId);
                $entityIdField = $attribute->getBackend()->getEntityIdField();
                $valueRow = array(
                    $entityIdField => $entityId,
                    'entity_type_id' => $newObject->getEntityTypeId(),
                    'store_id' => $newObject->getStoreId(),
                    'attribute_id'=>$attrId,
                    'value'=>$value,
                );
                $this->_write->insert($attribute->getBackend()->getTable(), $valueRow);
            }
        }
        
        // update attribute values
        if (!empty($update)) {
            foreach ($update as $attrId=>$v) {
                $attribute = $this->getAttribute($attrId);
                $this->_write->update(
                    $attribute->getBackend()->getTable(), 
                    array('value'=>$v['value']), 
                    "value_id=".(int)$v['value_id']
                );
            }
        }
        
        // delete empty attribute values
        if (!empty($delete)) {
            foreach ($delete as $table=>$valueIds) {
                $this->_write->delete($table, $this->_write->quoteInto('value_id in (?)', $valueIds));
            }
        }
        
        return $this;
    }
    
    protected function _afterLoad(Varien_Object $object)
    {
        $this->walkAttributes('backend/afterLoad', array($object));
    }
    
    protected function _beforeSave(Varien_Object $object)
    {
        $this->walkAttributes('backend/beforeSave', array($object));
    }
    
    protected function _afterSave(Varien_Object $object)
    {
        $this->walkAttributes('backend/afterSave', array($object));
    }
    
    protected function _beforeDelete(Varien_Object $object)
    {
        $this->walkAttributes('backend/beforeDelete', array($object));
    }
    
    protected function _afterDelete(Varien_Object $object)
    {
        $this->walkAttributes('backend/afterDelete', array($object));
    }
    
    protected function _getDefaultAttributeModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_ATTRIBUTE_MODEL; 
    }
    
    protected function _afterSetConfig()
    {
        $attributes = $this->_config->attributes;
        if (empty($attributes->entity_type_id)) {
            $attributes->addChild('entity_type_id', '');
        }
        if (empty($attributes->created_at)) {
            $attributes->addChild('created_at', '');
        }
        if (empty($attributes->updated_at)) {
            $attributes->addChild('updated_at', '');
        }
    }
}