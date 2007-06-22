<?php
/**
 * Entity resource model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Mysql4_Entity
{
    protected $_read;
    protected $_write;
    
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        
        $this->_read        = $resource->getConnection('core_read');
        $this->_write       = $resource->getConnection('core_write');
    }
    
    public function getIdName()
    {
        return 'entity_id';
    }
    
    public function load($entityId, Mage_Core_Model_Entity_Type $entityType)
    {
        
    }
    
    public function save($entity)
    {
        
    }
    
    public function delete($entity)
    {
        
    }
    
    public function getAttributes()
    {
        
    }
}
