<?php
/**
 * Entity type mysql4 resource model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Mysql4_Entity_Type
{
    protected $_entityTypeTable;
    
    /**
     * Read data connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;
    
    /**
     * Write data connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        
        $this->_entityTypeTable = $resource->getTableName('core/entity_type');
        $this->_read            = $resource->getConnection('core_read');
        $this->_write           = $resource->getConnection('core_write');
    }
    
    public function getIdFieldName()
    {
        return 'entity_type_id';
    }
    
    public function load(Mage_Core_Model_Entity_Type $type, $typeId)
    {
        if (is_string($typeId)) {
            $condition = $this->_read->quoteInto('entity_code=?', $typeId);
        }
        elseif (is_numeric($typeId)) {
        	$condition = $this->_read->quoteInto('entity_type_id=?', $typeId);
        }
        else {
            Mage::throwException('Wrong type id');
        }
        
        $select = $this->_read->select()
            ->from($this->_entityTypeTable)
            ->where($condition);
            
        $type->setData($this->_read->fetchRow($select));
        
        if ($type->getId()) {
            $attributes = Mage::getResourceModel('core/entity_attribute_collection')
                ->setAttributeTable($type->getAttributesTableName())
                ->addEntityTypeFilter($type->getId())
                ->addStoreFilter()
                ->setPositionOrder()
                ->load();
                
            /**
             * @see  Varien_Object::__call()
             */
            $type->setAttributeCollection($attributes);
        }
        return $type;
    }
    
    public function save(Mage_Core_Model_Entity_Type $type)
    {
        
    }
    
    public function delete(Mage_Core_Model_Entity_Type $type)
    {
        
    }
}
