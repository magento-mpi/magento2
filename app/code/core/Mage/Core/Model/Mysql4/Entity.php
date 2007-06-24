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
        
        if (!$entity->getId()) {
            return $entity;
        }
        
        /**
         * Load entity attributes values
         */
        $attributeCollection = $entity->getAttributeCollection();
        
        $values = array();
        foreach ($attributeCollection as $attribute) {
        	if (!isset($values[$attribute->getTypeCode()])) {
        	    $values[$attribute->getTypeCode()] = $attribute->getType()->loadAttributesValues($entity);
        	}
        	$entity->bindAttribute($attribute, $values[$attribute->getTypeCode()]);
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
