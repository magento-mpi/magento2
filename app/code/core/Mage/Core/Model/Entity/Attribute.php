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
        $type = Mage::getModel((string)$config->model)
            ->setConfig($config);
        $this->setType($type)
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
        if ($this->getType()) {
            return $this->getType()->getCode();
        }
        Mage::throwException('Can not retrieve attribute type');
    }
    
    public function getValueFromTypeValues($typeValues)
    {
        foreach ($typeValues as $row) {
        	if ($row[$this->getIdFieldName()] == $this->getId()) {
        	    return $row[$this->getType()->getValueFieldName()];
        	}
        }
        return null;
    }

    public function saveValue()
    {
        
    }
    
}
