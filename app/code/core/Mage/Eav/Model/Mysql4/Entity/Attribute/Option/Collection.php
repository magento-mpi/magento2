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
    
    public function setStoreFilter($storeId=null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::getSingleton('core/store')->getId();
        }
        $this->getSelect()->join($this->_optionValueTable, $this->_optionValueTable.'.option_id=main_table.option_id', 'value')
            ->where(
                $this->getConnection()->quoteInto($this->_optionValueTable.'.store_id=?', $storeId)
            );
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
}
