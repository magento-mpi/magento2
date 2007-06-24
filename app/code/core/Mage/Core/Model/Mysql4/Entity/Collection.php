<?php
/**
 * Entity collection resource model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Mysql4_Entity_Collection extends Varien_Data_Collection_Db
{
    protected $_entity;
    
    public function __construct($type) 
    {
        
    }
    
    public function setEntity(Mage_Core_Model_Entity $entity)
    {
        $this->_entity = $entity;
        return $this;
    }
    
    public function addAttributeSelect()
    {
        
    }
    
    public function addAttributeFilter()
    {
        
    }
}
