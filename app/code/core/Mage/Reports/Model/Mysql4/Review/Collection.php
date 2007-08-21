<?php
/**
 * Report Reviews collection
 *
 * @package    Mage
 * @subpackage Reports
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */
 
class Mage_Reports_Model_Mysql4_Review_Collection extends Mage_Review_Model_Mysql4_Review_Collection
{
    protected function _construct()
    {
        $this->_init('review/review');
    }
    
    public function addProductFilter($productId)
    {
        $this->_sqlSelect
            ->where($this->_reviewTable.'.entity_pk_value = ?', $productId);
            
        return $this;
    }
        
    public function resetSelect()
    {
        parent::resetSelect();
        $this->_joinFields();
        return $this;
    }
    
    public function getSelectCountSql()
    {
        $countSelect = clone $this->_sqlSelect;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();
        
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count('.$this->_reviewTable.'.review_id) from ', $sql);

        return $sql;
    }
    
    public function setOrder($attribute, $dir='desc')
    {
        $fields = array(
            'nickname',
            'title',
            'detail',
            'created_at'
        );
        
        if (in_array($attribute, $fields)) {
                $this->_sqlSelect->order($attribute . ' ' . $dir);
        } else {
                parent::setOrder($attribute, $dir);    
        }
        
        return $this;
    }
    
}
?>