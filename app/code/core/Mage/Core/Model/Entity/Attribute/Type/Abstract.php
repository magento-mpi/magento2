<?php
/**
 * Entity attribute type
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Entity_Attribute_Type_Abstract implements Mage_Core_Model_Entity_Attribute_Type_Interface
{
    protected $_code;
    protected $_config;
    protected $_tableName;
    
    public function getCode()
    {
        return $this->_code.'_'.$this->_tableName;
        //Mage::throwException('Can not retrieve attribute type code');
    }
    
    public function getValueFieldName()
    {
        return 'value';
    }
    
    public function setConfig(Varien_Simplexml_Element $config)
    {
        $this->_config      = $config;
        $this->_tableName   = Mage::getSingleton('core/resource')->getTableName($config->resource->table);
        return $this;
    }
    
    
    public function loadAttributesValues($entity)
    {
        return $this->getResource()->loadAttributesValues($this->_tableName, $entity);
    }
    
    public function getTableName()
    {
        return $this->_tableName;
    }
    
    public function saveValue()
    {
        
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton((string)$this->_config->resource->model);
    }
}
