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
    
    protected $_entityTable;
    
    protected $_entityIdField;
    
    protected $_valueEntityIdField;
    
    protected $_valueTablePrefix;
    
    
    /**
     * Set connections for entity operations
     *
     * @param Zend_Db_Adapter_Abstract $read
     * @param Zend_Db_Adapter_Abstract $write
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function setConnection($read, $write=null)
    {
        $this->_read = $read;
        $this->_write = $write ? $write : $read;
        return $this;
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
            $config = Mage::getConfig()->getNode("global/entity/types/$type");
        } elseif ($type instanceof Mage_Core_Model_Config_Element) {
            $config = $type;
        } else {
            throw Mage::exception('Mage_Eav', 'Unknown parameter');
        }
        
        if (!$config) {
            throw Mage::exception('Mage_Eav', 'Invalid entity type '.$type);
        }

        $this->_config = $config;
        
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
            
            if (isset($this->_attributesById[$attribute])) {
                return $this->_attributesById[$attribute];
            }
            
            $attributes = $this->getConfig()->attributes;
            $config = false;
            foreach ($attributes as $attrName=>$attrConfig) {
                if ((int)$attrConfig->id===$attribute) {
                    $config = $attrConfig;
                    $attribute = $attrName;
                    break;
                }
            }
            
        } elseif (is_string($attribute)) {
            
            if (isset($this->_attributesByName[$attribute])) {
                return $this->_attributesByName[$attribute];
            }
            $config = $this->getConfig()->attributes->$attribute;
            
        } elseif ($attribute instanceof Mage_Core_Model_Config_Element) {
            
            $config = $attribute;
            $attribute = $config->getName();
            if (isset($this->_attributesByName[$attribute])) {
                return $this->_attributesByName[$attribute];
            }

        }
        
        if (empty($config)) {
            $instance = Mage::getModel('eav/entity_attribute');
            $instance->setConfig(new Mage_Core_Model_Config_Element("<$attribute><backend/></$attribute>"));
            return $instance;
        }
        if (empty($config->model)) {
            $config->addChild('model', Mage_Eav_Model_Entity::DEFAULT_ATTRIBUTE_MODEL);
        }
        
        $instance = Mage::getModel((string)$config->model)
            ->setConfig($config)->setEntity($this);
            
        $this->_attributesByName[$attribute] = $instance;
        $this->_attributesById[$instance->getId()] = $instance;
        $this->_attributesByTable[$instance->getBackend()->getTable()][$attribute] = $instance;
        
        return $instance;
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
            if (empty($this->_valueEentityIdField)) {
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
            if (empty($prefix)) {
                $prefix = Mage_Eav_Model_Entity::DEFAULT_VALUE_TABLE;
            }
            $this->_valueTablePrefix = Mage::getSingleton('core/resource')->getTableName($prefix);
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
        return !$attrInstance || $attrInstance->getBackend()->isStatic();
    }

    /**
     * Load entity's attributes into current object
     *
     * @param integer $entityId
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function load($entityId)
    {
        if (!$this->_read) {
            throw Mage::exception('Mage_Eav', 'No connection available');
        }
        
        $object = $this->getObject();
        
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
        
        $this->loadAllAttributes();        
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
        
        return $this;
    }

    /**
     * Save entity's attributes into current object's resource
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function save()
    {
        if (!$this->_write) {
            throw Mage::exception('Mage_Eav', 'No connection available');
        }
        
        $this->_write->beginTransaction();
        
        try {
            $this->_processSaveData($this->_collectSaveData());
        } catch (Exception $e) {
            $this->_write->rollback();
            throw $e;
        }
        $this->_write->commit();

        #$this->_write->save($this);
        return $this;
    }
    
    protected function _collectSaveData()
    {
        $newObject = $this->getObject();
        $newData = $newObject->getData();
        
        $entityId = $newObject->getData($this->getEntityIdField());
        if (!empty($entityId)) {
            // get current data in db for this entity
            $origEntity = clone $this;
            $origObject = clone $newObject;
            $origObject->setData(array());
            $origEntity->setObject($origObject)->load($entityId);
            $origData = $origObject->getData();
            // drop attributes that are unknown in new data
            foreach ($origData as $k=>$v) {
                if (!isset($newData[$k])) {
                    unset($origData[$k]);
                    continue;
                }
            }
        }
        
        $saveData = array();

        
        foreach ($newData as $k=>$v) {
            $attribute = $this->getAttribute($k);
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
            } elseif (!empty($v)) {
                $insert[$attrId] = $v;
            }
        }
        
        $result = compact('entityRow', 'insert', 'update', 'delete');
   
        return $result;
    }
    
    protected function _processSaveData($saveData)
    {
        extract($saveData);

        $entityIdField = $this->getEntityIdField();
        $entityId = $this->getObject()->getData($entityIdField);
        
        if (empty($entityId)) {
            // insert entity table row
            $this->_write->insert($this->getEntityTable(), $entityRow);
            $this->getObject()->setData($entityIdField, $this->_write->lastInsertId());
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
                    'entity_type_id' => $this->getTypeId(),
                    'store_id' => $this->getStoreId(),
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

    /**
     * Delete entity using current object's data
     * 
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function delete()
    {
        if (!$this->_write) {
            throw Mage::exception('Mage_Eav', 'No connection available');
        }
        $object = $this->getObject();
        
        if (is_numeric($object)) {
            $id = (int)$object;
        } elseif ($object instanceof Varien_Object) {
            $id = (int)$object->getData();
        }
        
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->getEntityTable(), $this->getEntityIdField()."=".$id);
        } catch (Exception $e) {
            $this->_write->rollback();
            throw $e;
        }
        $this->_write->commit();
        
        #$this->_write->delete($this);
        return $this;
    }
}