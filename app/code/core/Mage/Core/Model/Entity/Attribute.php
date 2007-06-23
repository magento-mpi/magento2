<?php
/**
 * Entity attribute model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Entity_Attribute extends Varien_Object
{
    protected $_valueTableName;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('core/entity_attribute');
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
    
    public function setConfig($config)
    {
        $this->setType(Mage::getModel((string)$config->model));
        $this->setData('config', $config);
        return $this;
    }
    
    /**
     * Retrieve attribute value store table
     *
     * @return string
     */
    public function getValueTableName()
    {
        if ($this->_valueTableName) {
            return $this->_valueTableName;
        }
        
        /**
         * @see  Varien_Object::__call()
         */
        if ($config = $this->getConfig()) {
            $this->_valueTableName = Mage::getSingleton('core/resource')->getTableName((string)$config->resourceTable);
            return $this->_valueTableName;
        }
        Mage::throwException('Can not retrieve config for attribute "'.$this->getAttributeCode().'"');
    }
    
    public function getValueSelect()
    {
        return $this->getResource()->getValueSelect($this);
    }
    
    public function getValueColumns()
    {
        return $this->getResource()->getValueColumns();
    }
}
