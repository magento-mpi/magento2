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
abstract class Mage_Core_Model_Entity_Abstract extends Varien_Object implements Mage_Core_Model_Entity_Interface
{
    protected $_type;
    protected $_cacheObject;
    
    /**
     * Entity constructor
     * 
     * Initialize entity type object
     *
     * @todo  Make type objects sleep for type caching
     * @param string $type Entity type code
     */
    public function __construct($type, $useTypeCache = false) 
    {
        $useTypeCache = false;
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
        
        if ($useTypeCache) {
            $this->_initCacheObject();
            if($this->_loadTypeCache($type)){
                return $this;
            }
            else {
                $this->_initType($type);
            }
            $this->_saveTypeCache($type);
        }
        else {
            $this->_initType($type);
        }
    }
    
    protected function _initType($type)
    {
        if (is_string($type)) {
            $this->_type = Mage::getModel('core/entity_type')->load($type);
        }
        elseif (is_array($type) && isset($type['type'])) {
        	$this->_type = Mage::getModel('core/entity_type')->load($type['type']);
        }
        else {
            Mage::throwException('Wrong entity type parameter');
        }
        return $this;
    }
    
    protected function _initCacheObject()
    {
        $frontendOptions = array(
            'lifetime' => 7200,
            'automatic_serialization' => true
        );
        $backendOptions = array();
        
        $this->_cacheObject = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        return $this;
    }
    
    protected function _loadTypeCache($type)
    {
        if ($this->_type = $this->_cacheObject->load($type)) {
            return true;
        }
        return false;
    }
    
    protected function _saveTypeCache($type)
    {
        $this->_cacheObject->save($this->_type, $type);
        return $this;
    }
    
    /**
     * Retrieve entity type
     *
     * @return Mage_Core_Model_Entity_Type
     */
    public function getTypeObject()
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
        return $this->getTypeObject()->getAttributeCollection();
    }
    
    public function getAttributesCollection()
    {
        return $this->getAttributeCollection();
    }
    
    /**
     * Retrieve entity storage table name
     *
     * @return string
     */
    public function getValueTableName()
    {
        return $this->getTypeObject()->getEntityTableName();
    }
    
    /**
     * Bind attribute and attribute value for entity
     *
     * @param   Mage_Core_Model_Entity_Attribute_Interface $attribute
     * @param   array $attributeTypeValues
     * @return  Mage_Core_Model_Entity
     */
    public function bindAttribute(Mage_Core_Model_Entity_Attribute_Interface $attribute, $attributeTypeValues)
    {
        $value = $attribute->getValueFromTypeValues($attributeTypeValues);
        $this->setData($attribute->getAttributeCode(), $value);
        return $this;
    }
    
    public function getEmptyCollection()
    {
        return Mage::getResourceModel('core/entity_collection')
            ->setEntityObject($this);
    }
}
