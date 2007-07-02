<?php
/**
 * Entity attribute type resource model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
abstract class Mage_Core_Model_Mysql4_Entity_Attribute_Type_Abstract
{
    protected $_read;
    protected $_write;
    
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        
        $this->_read        = $resource->getConnection('core_read');
        $this->_write       = $resource->getConnection('core_write');
    }
    
    public function loadAttributesValues($tableName, $entity)
    {
        $select = $this->_read->select()
            ->from($tableName)
            ->where($this->_read->quoteInto($entity->getIdFieldName().'=?', $entity->getId()));
        return $this->_read->fetchAll($select);
    }
}
