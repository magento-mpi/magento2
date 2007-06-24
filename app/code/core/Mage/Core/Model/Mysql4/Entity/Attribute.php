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
        
        $this->_attributeTable  = $resource->getTableName('core/attribute');
        $this->_read            = $resource->getConnection('core_read');
        $this->_write           = $resource->getConnection('core_write');
    }
    
    public function getIdFieldName()
    {
        return 'attribute_id';
    }
    
    public function load(Mage_Core_Model_Entity_Attribute $attribute, $attributeId)
    {
        
    }
    
    public function save($attribute)
    {
        
    }
    
    public function delete($attribute)
    {
        
    }
    
    public function getValueSelect(Mage_Core_Model_Entity_Attribute $attribute)
    {
        return $this->_read->select()
            ->from($attribute->getValueTableName())
            ->where($this->_read->quoteInto($this->getIdFieldName().'=?', $attribute->getId()));
    }

    public function getValueColumns()
    {
        return array($this->getIdFieldName(), 'value');
    }
}
