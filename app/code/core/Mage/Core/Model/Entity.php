<?php
/**
 * Entity base model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Entity extends Varien_Object
{
    protected $_type;
    
    /**
     * Entity constructor
     * 
     * Initialize entity type object
     *
     * @param string $type Entity type code
     */
    public function __construct($type) 
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
        
        if (is_string($type)) {
            $this->_type = Mage::getModel('core/entity_type')->load($type);
        }
        elseif (is_array($type) && isset($type['type'])) {
        	$this->_type = Mage::getModel('core/entity_type')->load($type['type']);
        }
        else {
            Mage::throwException('Wrong entity type parameter');
        }
    }
    
    /**
     * Retrieve entity type
     *
     * @return Mage_Core_Model_Entity_Type
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Retrieve entity resource model
     *
     * @return Object
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('core/entity');
    }
    
    /**
     * Load entity
     * 
     * Load entity and values of entity attributes
     * 
     * @param   mixed $entityId
     * @return  Mage_Core_Model_Entity
     */
    public function load($entityId)
    {
        $this->getResource()->load($this, $entityId);
        return $this;
    }
    
    /**
     * Save entity
     *
     * @return Mage_Core_Model_Entity
     */
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    /**
     * Delete entity
     *
     * @return Mage_Core_Model_Entity
     */
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
    
    /**
     * Retrieve entity attributes loaded collection
     *
     * @return Varien_Data_Collection
     */
    public function getAttributeCollection()
    {
        return $this->getType()->getAttributeCollection();
    }
    
    /**
     * Retrieve entity storage table name
     *
     * @return string
     */
    public function getValueTableName()
    {
        return $this->getType()->getEntityTableName();
    }
    
    /**
     * Bind attribute and attribute value for entity
     *
     * @param Mage_Core_Model_Entity_Attribute_Interface $attribute
     * @param unknown_type $attributeTypeValues
     */
    public function bindAttribute(Mage_Core_Model_Entity_Attribute_Interface $attribute, $attributeTypeValues)
    {
        $value = $attribute->getValueFromTypeValues($attributeTypeValues);
        $this->setData($attribute->getAttributeCode(), $value);
    }
    
    public function getCollection()
    {
        return Mage::getResourceModel('core/entity_collection')
            ->setEntity($this);
    }
}
