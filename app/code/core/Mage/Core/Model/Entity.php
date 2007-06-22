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
        $this->setIdFieldName($this->getResource()->getIdName());
        $this->_type = Mage::getModel('core/entity_type')->load($type);
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
        $this->setData($this->getResource()->load($entityId, $this->_type));
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
    
    public function getAttributes()
    {
        return $this->getType()-getAttributes();
    }
}
