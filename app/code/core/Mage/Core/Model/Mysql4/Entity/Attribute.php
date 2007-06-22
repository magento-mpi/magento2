<?php
/**
 * Entity attribute mysql4 resource model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Mysql4_Entity_Attribute
{
    protected $_attributeTable;
    
    protected $_read;
    protected $_write;
    
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        
        $this->_attributeTable  = $resource->getTableName('core/attribute');
        $this->_read            = $resource->getConnection('core_read');
        $this->_write           = $resource->getConnection('core_write');
    }
    
    public function getIdName()
    {
        return 'attribute_id';
    }
    
    public function load($attributeId)
    {
        
    }
    
    public function save($attribute)
    {
        
    }
    
    public function delete($attribute)
    {
        
    }
}
