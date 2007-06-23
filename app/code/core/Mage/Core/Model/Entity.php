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
    
    public function getResource()
    {
        return Mage::getResourceSingleton('core/entity');
    }
    
    public function load($entityId)
    {
        $this->getResource()->load($this, $entityId);
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
    
    /**
     * Retrieve entity attributes
     *
     * @return Varien_Data_Collectio
     */
    public function getAttributeCollection()
    {
        return $this->getType()->getAttributeCollection();
    }
    
    public function getValueTableName()
    {
        return $this->getType()->getEntityTableName();
    }
}
