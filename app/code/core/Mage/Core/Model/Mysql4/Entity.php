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
    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * Write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        
        $this->_read        = $resource->getConnection('core_read');
        $this->_write       = $resource->getConnection('core_write');
    }
    
    public function getIdFieldName()
    {
        return 'entity_id';
    }
    
    public function load(Mage_Core_Model_Entity $entity, $entityId)
    {
        /**
         * Load base entity data
         */
        $select = $this->_read->select()
            ->from($entity->getValueTableName())
            ->where($this->getIdFieldName().'=?', $entityId);

        $entity->setData($this->_read->fetchRow($select));
        
        /**
         * Load entity attributes
         */
        $attributeCollection = $entity->getAttributeCollection();
        
        // prepare value tables unions
        $unions = array();
        foreach ($attributeCollection as $attribute) {
        	if (!isset($unions[$attribute->getValueTableName()])) {
        	    $select = $this->_read->select()
        	       ->from($attribute->getValueTableName(), $attribute->getValueColumns())
        	       ->where($this->_read->quoteInto($this->getIdFieldName().'=?', $entityId));
        	       
        	    /**
        	     * @todo add store condition
        	     */
        	    $unions[$attribute->getValueTableName()] = $select;
        	}
        }
        
        $sql = implode(" \nUNION \n", $unions);
        
        $data = $this->_read->fetchPairs($sql);
        
        if (!empty($data)) {
            foreach ($attributeCollection as $attribute) {
            	if (isset($data[$attribute->getId()])) {
            	    $entity->setData($attribute->getAttributeCode(), $data[$attribute->getId()]);
            	}
            }
        }
        
        return $entity;
    }
    
    public function save($entity)
    {
        
    }
    
    public function delete($entity)
    {
        
    }
}
