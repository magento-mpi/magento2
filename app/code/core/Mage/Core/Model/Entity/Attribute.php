<?php
/**
 * Entity attribute model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @methods     getType
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Entity_Attribute extends Varien_Object implements Mage_Core_Model_Entity_Attribute_Interface
{
    public function __construct() 
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }
    
    public function getCode()
    {
        return $this->getAttributeCode();
    }
    
    /**
     * Retrieve arrtibute resource model
     *
     * @return Object
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('core/entity_attribute');
    }
    
    /**
     * Load attribute
     *
     * @param   int|string $attributeId
     * @return  Mage_Core_Model_Entity_Attribute
     */
    public function load($attributeId)
    {
        $this->getResource()->load($this, $attributeId);
        return $this;
    }
    
    /**
     * Save attribute
     *
     * @return Mage_Core_Model_Entity_Attribute
     */
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    /**
     * Delete attribute
     *
     * @return Mage_Core_Model_Entity_Attribute
     */
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
    
    /**
     * Set attribute configuration and type object
     *
     * @param   Varien_Simplexml_Element $config
     * @return  Mage_Core_Model_Entity_Attribute
     */
    public function setConfig(Varien_Simplexml_Element $config)
    {
        $className = (string)$config->model;
        if (!$className) {
            $className = 'core/entity_attribute_type_default';
        }
        $type = Mage::getModel($className)->setConfig($config);
        /**
         * @see  Varien_Object::__call()
         */    
        $this->setTypeObject($type)
            ->setData('config', $config);
        return $this;
    }
    
    public function setType(Mage_Core_Model_Entity_Attribute_Type_Interface $type)
    {
        $this->setData('type', $type);
        return $this;
    }
    
    public function getTypeCode()
    {
        if ($this->getTypeObject()) {
            return $this->getTypeObject()->getCode();
        }
        Mage::throwException('Can not retrieve attribute type');
    }
    
    public function getValueFromTypeValues($typeValues)
    {
        foreach ($typeValues as $row) {
        	if ($row[$this->getIdFieldName()] == $this->getId()) {
        	    return $row[$this->getTypeObject()->getValueFieldName()];
        	}
        }
        return null;
    }
    
    public function getJoinTableName()
    {
        return $this->getTypeObject()->getTableName().' AS '.$this->getJoinTableAlias();
    }
    
    public function getJoinTableAlias()
    {
        return $this->getTypeCode().'_'.$this->getAttributeCode();
    }
    
    public function getJoinCondition($entity)
    {
        $condition = $this->getJoinTableAlias().'.'.$entity->getIdFieldName().'='
                    .$entity->getValueTableName().'.'.$entity->getIdFieldName()
                    .' AND '.$this->getJoinTableAlias().'.'.$this->getIdFieldName().'='.$this->getId();
        return $condition;
    }
    
    public function getJoinColumns($withAlias = false)
    {
        $columns = $this->getJoinTableAlias().'.'.$this->getTypeObject()->getValueFieldName();
        if ($withAlias) {
            $columns.= ' AS '.$this->getAttributeCode();
        }
        return $columns;
    }
    
    public function getFormFieldName()
    {
        return 'attribute['.$this->getId().']';
    }

    public function saveValue()
    {
        
    }
    
}
