<?php
/**
 * Entity type model
 *
 * @package     Mage
 * @subpackage  Core
 * @method      
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Entity_Type extends Varien_Object 
{
    protected $_config;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('core/entity_type');
    }
    
    public function load($typeId)
    {
        $this->_config = Mage::getConfig()->getNode('global/entities/'.$typeId);
        if (false === $this->_config) {
            Mage::throwException('Can not retrieve config for entity type "'.$typeId.'"');
        }
        $this->getResource()->load($this, $typeId);        
        return $this;
    }
    
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
    
    public function setAttributeCollection($collection)
    {
        $types = (array) $this->_config->attribute->types;
        foreach ($collection as $attribute) {
        	if (isset($types[$attribute->getAttributeType()])) {
        	    /**
        	     * @see  Varien_Object::__call()
        	     */
        	    $attribute->setConfig($types[$attribute->getAttributeType()]);
        	}
        	else {
        	    Mage::throwException('Can not retrieve type("'.$attribute->getAttributeType().'") config by attribute "'.$attribute->getAttributeCode().'"');
        	}
        }
        $this->setData('attribute_collection', $collection);
    }
    
    public function getAttributesTableName()
    {
        if ($this->getData('attributes_table_name')) {
            $tableName = $this->getData('attributes_table_name');
        }
        else {
            $tableName = Mage::getSingleton('core/resource')->getTableName((string)$this->_config->attribute->resourceTable);
            $this->setData('attributes_table_name', $tableName);
        }
        return $tableName;
    }
    
    public function getEntityTableName()
    {
        if ($this->getData('entity_table_name')) {
            $tableName = $this->getData('entity_table_name');
        }
        else {
            $tableName = Mage::getSingleton('core/resource')->getTableName((string)$this->_config->resourceTable);
            $this->setData('entity_table_name', $tableName);
        }
        return $tableName;
    }
}
