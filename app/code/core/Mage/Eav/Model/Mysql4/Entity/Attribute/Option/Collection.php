<?php
/**
 * Entity attribute option collection
 *
 * @package     Mage
 * @subpackage  Eav
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Eav_Model_Mysql4_Entity_Attribute_Option_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_optionValueTable;
    
    public function _construct() 
    {
        $this->_init('eav/entity_attribute_option');
        $this->_optionValueTable = Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value');
    }
    
    public function setAttributeFilter($setId)
    {
        $this->getSelect()->where('main_table.attribute_id=?', $setId);
        return $this;
    }
    
    public function setStoreFilter($storeId=null, $useDefaultValue=true)
    {
        if (is_null($storeId)) {
            $storeId = Mage::getSingleton('core/store')->getId();
        }
        if ($useDefaultValue) {
            $this->getSelect()
                ->join(array('store_default_value'=>$this->_optionValueTable), 
                    'store_default_value.option_id=main_table.option_id', 
                    array('default_value'=>'value'))                
                ->joinLeft(array('store_value'=>$this->_optionValueTable), 
                    'store_value.option_id=main_table.option_id AND '.$this->getConnection()->quoteInto('store_value.store_id=?', $storeId), 
                    array('store_value'=>'value',
                    'value' => new Zend_Db_Expr('IFNULL(store_value.value,store_default_value.value)')))
                ->where($this->getConnection()->quoteInto('store_default_value.store_id=?', 0));
        }
        else {
            $this->getSelect()
                ->joinLeft(array('store_value'=>$this->_optionValueTable), 
                    'store_value.option_id=main_table.option_id AND '.$this->getConnection()->quoteInto('store_value.store_id=?', $storeId), 
                    'value')
                ->where($this->getConnection()->quoteInto('store_value.store_id=?', $storeId));
        }
            
        return $this;
    }
    
    public function setIdFilter($id)
    {
        $this->getSelect()->where('main_table.option_id=?', $id);
        return $this;
    }
    
    public function toOptionArray()
    {
        return $this->_toOptionArray('option_id', 'value');
    }
    
    public function setPositionOrder($dir='asc')
    {
        $this->setOrder('main_table.sort_order', $dir);
        return $this;
    }
}
